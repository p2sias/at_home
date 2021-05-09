<template>
    <v-container>
        <v-col cols="10"  sm="12">
            <v-card>
                <div class="d-flex justify-space-between">
                    <v-card-title>Toutes les sessions</v-card-title>
                    <v-icon style="cursor:pointer;" @click="createDialog = true" size="40">mdi-plus</v-icon>
                </div>
                <div>
                    <v-card v-for="session in sessions" :key="session.id" class="d-flex justify-space-between align-center flex-wrap">
                        <v-card-title>{{session.title}}</v-card-title>
                        <p>démarée le {{(session.start_date).split('T')[0]}} | termine le {{(session.end_date).split('T')[0]}}</p>
                        <v-card-actions>
                            <v-btn :to="{ name: 'AdminSession', params: { id: session.id }}">
                                ...
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </div>
            </v-card>
        </v-col>
        <v-dialog

            v-model="createDialog"
            max-width="400px"
            >
                <v-card>
                        <v-card-title class="text-break d-flex justify-space-between">
                            <h3>Créer un session</h3>
                            <div class="d-flex justify-space-between">
                                <v-icon v-if="!modifyLoading" color="green" @click="saveSession()">mdi-content-save</v-icon>
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

                            <div class="d-flex justify-space-between">
                                <v-menu
                                    ref="startDateMenu"
                                    v-model="startDate"
                                    :close-on-content-click="false"
                                    :return-value.sync="formdat.start_date"
                                    transition="scale-transition"
                                    offset-y
                                    min-width="auto"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="formdat.start_date"
                                        label="Start date"
                                        prepend-icon="mdi-calendar"
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                    ></v-text-field>
                                    </template>
                                    <v-date-picker
                                    v-model="formdat.start_date"
                                    no-title
                                    scrollable
                                    :min="new Date().toISOString().substr(0, 10)"
                                    >
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="menu = false"
                                    >
                                        Cancel
                                    </v-btn>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="$refs.startDateMenu.save(formdat.start_date)"
                                    >
                                        OK
                                    </v-btn>
                                    </v-date-picker>
                                </v-menu>
                                <v-menu
                                    ref="endDateMenu"
                                    v-model="endDate"
                                    :close-on-content-click="false"
                                    :return-value.sync="formdat.end_date"
                                    transition="scale-transition"
                                    offset-y
                                    min-width="auto"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="formdat.end_date"
                                        label="End date"
                                        prepend-icon="mdi-calendar"
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                    ></v-text-field>
                                    </template>
                                    <v-date-picker
                                    v-model="formdat.end_date"
                                    no-title
                                    scrollable
                                    :min="new Date().toISOString().substr(0, 10)"
                                    >
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="menu = false"
                                    >
                                        Cancel
                                    </v-btn>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="$refs.endDateMenu.save(formdat.end_date)"
                                    >
                                        OK
                                    </v-btn>
                                    </v-date-picker>
                                </v-menu>
                            </div>

                            <v-text-field
                                required
                                v-model="formdat.price"
                                label="Prix"
                                prefix="€"
                                :rules="[v => !!v || 'Entrez un prix']"
                            ></v-text-field>

                            <v-text-field
                                required
                                v-model="formdat.max_participant"
                                label="Nombre de participants"
                                :rules="[v => !!v || 'Entrez le nb de participant max']"
                            ></v-text-field>
                        </v-form>
                        </v-col>

                </v-card>
        </v-dialog>
    </v-container>
</template>

<script lang="ts">
/* eslint-disable  @typescript-eslint/no-explicit-any */
import { Component, Vue } from 'vue-property-decorator';
import axios from 'axios';

@Component({
    mounted() {
        let user = this.$store.getters.currentUser
        if(user == null) this.$router.push('/');
        if(user.auth_level == 1) this.$router.push('/dashboard');
        if(user.auth_level == 4) this.$router.push('/admin/posts');
        this.$store.dispatch('getSessions');
    }
})
export default class AdminPanel extends Vue {

    private createDialog = false;
    private createValid = true;
    private startDate = false;
    private endDate = false;
    private user: any = this.$store.getters.currentUser;
    private modifyLoading = false;

    private formdat: any = {
        title: '',
        start_date: new Date().toISOString().substr(0, 10),
        end_date: new Date().toISOString().substr(0, 10),
        max_participant: '',
        price : 0,
        status: 'ofj',
        main_picture: '',
        user_id: this.user.id,
    }

    private resetForm(): void {
        this.formdat = {
            title: '',
            start_date: new Date().toISOString().substr(0, 10),
            end_date: new Date().toISOString().substr(0, 10),
            max_participant: '',
            status: 'ofj',
            price : 0,
            main_picture: '',
            user_id: this.user.id,
        }
    }

   private get sessions(): any {
       return this.$store.getters.allSessions
   }

   private async saveSession(): Promise<void>
   {
        this.modifyLoading = true;

        await axios.post('http://localhost:8000/api/session/create', this.formdat, {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer '+this.user.api_token,
            }
        }).then((response: any) => {
            console.log(response)
           this.$store.dispatch('getSessions');
           this.createDialog = false;
           this.modifyLoading = false;
        })
        .catch((err: any) => {console.log(JSON.stringify(err.message))});
   }

}
</script>
