<template>
    <v-container>
        <v-row>
            <v-col sm="6" cols="12">
                <div class="d-flex justify-space-between">
                    <h2>Challenges</h2>
                    <v-icon style="cursor:pointer;" @click="createDialog = true" size="40">mdi-plus</v-icon>
                </div>
                <v-col v-for="challenge in session.challenges" :key="challenge.id">
                    <ChallengeDetails :challenge="challenge" mode="admin" />
                </v-col>
            </v-col>
            <v-col sm="6" cols="12">
                <div class="d-flex justify-space-between">
                    <h2>Participants</h2>
                </div>
                <div>
                    <v-card v-for="user in session.users" :key="user.id">
                        <v-card-title>{{user.pseudo}}</v-card-title>
                        <v-card-text>{{user.surname}} {{user.name}} {{user.email}}</v-card-text>
                    </v-card>
                </div>
            </v-col>
            <v-col sm="6" cols="12">
                <h2>Validations</h2>
                <div v-if="!validationLoading">
                    <div v-for="validation in validations" :key="validation.id">
                        <ChallengeValidator v-if="validation.status == 'prog'" :challenge="validation.challenge" :user="validation.user" :validation="validation" />
                    </div>
                </div>
                <v-progress-circular
                    v-else
                    indeterminate
                    color="primary"
                ></v-progress-circular>


            </v-col>
            <v-col sm="6" cols="12">
                <h2>Validations traitées</h2>
                <div v-if="!validationLoading">
                    <div v-for="validation in validations" :key="validation.id">
                        <ChallengeValidator v-if="validation.status != 'prog'" :challenge="validation.challenge" :user="validation.user" :validation="validation" />
                    </div>
                </div>
                <v-progress-circular
                    v-else
                    indeterminate
                    color="primary"
                ></v-progress-circular>
            </v-col>

            <v-dialog

            v-model="createDialog"
            max-width="400px"
            >
                <v-card>
                        <v-card-title class="text-break d-flex justify-space-between">
                            <h3>Ajouter un challenge</h3>
                            <div class="d-flex justify-space-between">
                                <v-icon v-if="!modifyLoading" color="green" @click="saveChallenge()">mdi-content-save</v-icon>
                                <v-icon v-if="!modifyLoading" color="red" @click="resetForm()">mdi-refresh</v-icon>
                                <v-icon v-if="!modifyLoading" color="red" @click="createDialog = false">mdi-close</v-icon>
                                <v-progress-circular v-else
                                        indeterminate
                                        color="primary"
                                ></v-progress-circular>
                            </div>
                        </v-card-title>

                        <v-col>
                            <v-form
                        ref="modify"
                        v-model="createValid"
                        >
                            <v-text-field
                                v-model="formdat.title"
                                label="Titre"
                                max="50"
                                :rules="[v => !!v || 'Entrez un titre']"
                            ></v-text-field>

                            <v-textarea
                                required
                                v-model="formdat.description"
                                max="2500"
                                :rules="[v => !!v || 'Entrez une description']"
                                label="Description"
                            >

                            </v-textarea>

                            <v-slider
                                v-model="formdat.points"
                                color="orange"
                                label="Points"
                                min="1"
                                max="300"
                                thumb-label
                                ></v-slider>
                        </v-form>
                        </v-col>

                </v-card>
        </v-dialog>
        </v-row>
    </v-container>
</template>

<script lang="ts">
/* eslint-disable  @typescript-eslint/no-explicit-any */
import { Component, Vue } from 'vue-property-decorator';
import ChallengeDetails from '../../../Components/ChallengeDetails.vue'
import ChallengeValidator from '../../../Components/admin/ChallengeValidator.vue'
import axios from 'axios';

@Component({
    components: {
        ChallengeDetails,
        ChallengeValidator
    },
    created() {
        let user = this.$store.getters.currentUser
        if(user == null) this.$router.push('/');
        else if(user.auth_level == 1) this.$router.push('/dashboard');
        else if(user.auth_level == 4) this.$router.push('/admin/posts');
        else {
            this.$store.dispatch('getSessions');
            this.$store.dispatch('getAllValidationsBySessionId', this.$route.params.id);
        }
    }
})
export default class SessionDetails extends Vue {
    private createValid = true;
    private createDialog = false;
    private modifyLoading = false;

    private formdat: any = {
        title: '',
        description: '',
        points: ''
    }

    private async saveChallenge(): Promise<void>
    {
        this.modifyLoading = true;
        let data = this.formdat;
        data.session_id = this.$route.params.id;
        data.user_id = this.$store.getters.currentUser.id;

        let headers = {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer '+this.$store.getters.apiToken,
        }

        await axios.post('http://localhost:8000/api/challenge/create', data, {headers: headers}).then((response) => {
            console.log(response);
            this.$store.dispatch('getSessions');
            this.modifyLoading = false;
            this.createDialog = false;
            this.resetForm();
        });
    }

    private resetForm(): void {
        this.formdat = {
            title: '',
            description: '',
            points: ''
        };
    }

    private get session(): any {
        return this.$store.getters.getSessionById(this.$route.params.id);
    }

    private get validationLoading(): boolean {
        return this.$store.getters.validationsLoading;
    }

    private get validations(): any {
        return this.$store.getters.getValidationsBySession;
    }

    private addChallenge(): void {
        return;
    }

}
</script>
