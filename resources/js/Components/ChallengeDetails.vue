<template>
    <v-main>
         <v-card class="d-flex justify-space-between flex-wrap">
                <v-card-title>{{challenge.title}}</v-card-title>
                <v-card-text v-if="isValidation">
                    <span v-if="validation.status == 'prog'" style="color:blue">votre demande de validation sera traitée sous peu</span>
                    <span v-else-if="validation.status == 'reject'" style="color:red;">votre demande validation à été rejetée</span>
                    <span v-else style="color:green;">votre demande de validation à été acceptée</span>
                </v-card-text>
                <v-card-actions>
                    <v-btn v-if="isJoined && !isValidation" @click="details()">Voir</v-btn>
                    <v-btn v-if="!isJoined && !isValidation && !isAdmin" @click="details()">Rejoindre</v-btn>
                    <v-btn v-if="isValidation && !isAdmin && validation.status != 'prog'" @click="details()">
                        <span v-if="validation.status == 'valid'" style="color:green;">Validée</span>
                        <span v-else-if="validation.status == 'reject'" style="color:red;">Rejetée</span>
                        <v-btn v-else @click="details()" disabled>En cours</v-btn>
                    </v-btn>
                    <v-btn v-if="isAdmin" @click="details()">Modifier</v-btn>
                </v-card-actions>
        </v-card>


        <v-dialog
        v-if="!isAdmin"
        v-model="detailsDialog"
        max-width="1000px"
        >
            <v-card
                class="mx-auto"

            >
                <v-img
                class="white--text align-end"
                height="200px"
                src="https://cdn.vuetifyjs.com/images/cards/docks.jpg"
                >
                <v-card-title class="text-break">{{challenge.title}}</v-card-title>
                </v-img>

                <v-card-text class="text-primary">
                    <div>
                        {{challenge.description}}
                    </div>
                    <div>
                        Points : {{challenge.points}}
                    </div>
                </v-card-text>

                <v-col v-if="isJoined">
                    <v-col class="d-flex flex-wrap justify-content-space-between">
                        <v-col>

                                 <v-form
                                    v-model="isValid"
                                >
                                    <v-card  min-width="200px"  class="d-flex flex-wrap">
                                        <v-file-input
                                            ref="file"
                                            v-on:change="getFileUpload($event)"
                                            truncate-length="15"
                                        ></v-file-input>
                                        <v-card-actions>
                                            <v-btn @click="uploadFile()" :disabled="!havePhoto">
                                                <v-icon v-if="!fileUploadLoading">
                                                    mdi-send
                                                </v-icon>
                                                <v-progress-circular v-if="fileUploadLoading"
                                                        indeterminate
                                                        color="primary"
                                                ></v-progress-circular>
                                            </v-btn>
                                        </v-card-actions>
                                    </v-card>
                                </v-form>

                        </v-col>

                        <v-col>
                            <v-card min-width="200px">
                                <v-card-title class="d-flex align-center justify-space-between">
                                    <span >Vos preuves</span>
                                    <v-progress-circular v-if="fileDeleteLoading"
                                                indeterminate
                                                color="primary"
                                    ></v-progress-circular>
                                </v-card-title>
                                <v-card class="d-flex justify-space-between align-center" v-for="proof in filesProof" :key="proof.id">
                                    {{proof.user_fileName}}
                                    <v-card-actions>
                                        <v-icon style="cursor:pointer;" @click="deleteFile(proof.id)">mdi-close</v-icon>
                                    </v-card-actions>
                                </v-card>
                            </v-card>
                        </v-col>
                    </v-col>
                </v-col>

                <v-col v-if="isValidation">
                    <v-card v-if="validation.status != 'prog'">
                        <v-card-title>Status : {{validation.status}}</v-card-title>
                            {{validation.comment}} <br />
                            Traité le : {{(validation.created_at).split('T')[0]}}
                    </v-card>
                </v-col>

                <v-card-actions class="d-flex flex-wrap">
                    <v-btn color="red" @click="detailsDialog = false"><v-icon>mdi-close</v-icon></v-btn>
                    <v-btn v-if="!isValidation && isJoined && !loading" color="green" @click="challengeControl('validate')">Valider</v-btn>
                    <v-btn v-if="!isValidation && isJoined && !loading" @click="challengeControl('leave')">Quitter le défi</v-btn>
                    <v-btn v-if="!isValidation && !isJoined && !loading" color="green" @click="challengeControl('join')">Rejoindre</v-btn>
                    <v-progress-circular v-if="loading"
                            indeterminate
                            color="primary"
                    ></v-progress-circular>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog
        v-else
        v-model="modifyDialog"
        max-width="400px"
        >
                <v-card>
                        <v-card-title class="text-break d-flex justify-space-between">
                            <h3>Modifier le challenge</h3>
                            <div class="d-flex justify-space-between">
                                <v-icon v-if="!modifyLoading" color="red" @click="deleteChallenge()">mdi-delete</v-icon>
                                <v-icon v-if="!modifyLoading" color="green" @click="modifyChallenge()">mdi-content-save</v-icon>
                                <v-icon v-if="!modifyLoading" color="red" @click="resetForm()">mdi-refresh</v-icon>
                                <v-icon v-if="!modifyLoading" color="red" @click="modifyDialog = false">mdi-close</v-icon>
                                <v-progress-circular v-else
                                        indeterminate
                                        color="primary"
                                ></v-progress-circular>
                            </div>
                        </v-card-title>

                        <v-col>
                            <v-form
                        ref="modify"
                        v-model="modifyValid"
                        >
                            <v-text-field
                                v-model="formData.title"
                                label="Titre"
                                max="50"
                                :rules="[v => !!v || 'Entrez un titre']"
                            ></v-text-field>

                            <v-textarea
                                required
                                v-model="formData.description"
                                max="2500"
                                :rules="[v => !!v || 'Entrez une description']"
                                label="Description"
                            >

                            </v-textarea>

                            <v-slider
                                v-model="formData.points"
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

        <v-dialog
            v-model="errorDialog"
            max-width="290"
            >
                <v-card>
                    <v-card-title class="headline" color="red">Erreur</v-card-title>
                    <v-card-text>Veuillez uploader au moins une preuve</v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="red"
                            text
                            @click="errorDialog = false"
                        > Ok </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
    </v-main>


</template>

<script lang="ts">
import { Component, Vue, Prop } from 'vue-property-decorator';
import axios from 'axios';

function getBase64(file: File) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result);
    reader.onerror = error => reject(error);
  });
}

@Component
export default class DashBoard extends Vue {
    @Prop() readonly challenge!: any;
    @Prop() readonly mode!: string;

    private detailsDialog = false;
    private modifyDialog = false;

    private loading = false;
    private isValid = false;
    private havePhoto = false;
    private uploadedFile: any;

    private modifyValid = false;

    private fileDeleteLoading = false;
    private fileUploadLoading = false;
    private modifyLoading = false;

    private savedFiles: any;

    private errorDialog = false;

    private user = this.$store.getters.currentUser;
    private api_token: string = this.$store.getters.apiToken;

    private initialField: any = {
        description: this.challenge.description,
        points: this.challenge.points,
        title: this.challenge.title,
        user_id: this.$store.getters.currentUser.id
    }


    private get isJoined(): boolean {
        if(this.mode == "current") return true;
        return false;
    }

    private get isAdmin(): boolean {
        if(this.mode == "admin") return true;
        return false;
    }

    private get isValidation(): boolean {
        if(this.mode == 'validating') return true;
        return false;
    }

    private details(){
        if(this.isAdmin) {
            this.resetForm();
            this.modifyDialog = true;
        }
        else this.detailsDialog = true;
    }

    private get validation()
    {
        const validations = this.$store.getters.userValidations;
        let selected: any;

        validations.forEach((validation: any) => {
            if(validation.challenge_id == this.challenge.id) selected = validation;
        });

        return selected;
    }

    private get filesProof(): any
    {
        const allFiles: any = this.$store.getters.userValidationsFiles
        let sorted: any = []

        allFiles.forEach((file: any) => {
            if(file.challenge_id == this.challenge.id)
            {
                sorted.push(file);
            }
        })

        return sorted
    }

    private async deleteChallenge()
    {
        this.modifyLoading = true;
        await axios.post('http://localhost:8000/api/challenge/'+this.challenge.id+'/delete',{user_id: this.user.id}, {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer '+this.api_token,
            }
        }).then((response: any) => {
            console.log(response)
            this.$store.dispatch('getSessions');
            this.modifyLoading = false;
            this.modifyDialog = false;
        })
        .catch((err: any) => {console.log(JSON.stringify(err.message))});
    }


    private cancel(): void {
        this.$emit('cancel')
    }

    private getFileUpload(file: File)
    {
        this.uploadedFile = file;
        this.havePhoto = true;
    }

    private get formData(): any {
        return this.initialField;
    }

    private validateModify (): void {
        (this.$refs.modify as any).validate()
    }


    private async uploadFile()
    {
        this.fileUploadLoading = true;
        const base64file = await getBase64(this.uploadedFile);

        await axios.post('http://localhost:8000/api/challenge/'+this.challenge.id+'/file/create', {file: base64file, user_id: this.user.id, user_fileName: this.uploadedFile.name}, {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer '+this.api_token,
            }
        }).then((response: any) => {
           this.$store.dispatch('getFilesByUser', this.user.id)
           this.fileUploadLoading = false;
           this.havePhoto = false;
           (this.$refs.file as any).reset();
        })
        .catch((err: any) => {console.log(JSON.stringify(err.message))});

    }

    private async deleteFile(file_id: number)
    {
        this.fileDeleteLoading = true;
        await axios.post('http://localhost:8000/api/challenge/file/'+file_id+'/delete', {user_id: this.user.id}, {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer '+this.api_token,
            }
        }).then((response: any) => {
           this.$store.dispatch('getFilesByUser', this.user.id)
           this.fileDeleteLoading = false;
        })
        .catch((err: any) => {console.log(JSON.stringify(err.message))});
    }

    private async modifyChallenge(): Promise<void> {
        this.modifyLoading = true;
        this.validateModify();
        if (!this.modifyValid){
            this.modifyLoading = false;
            return;
        }

        let newChallengeEntries: any = this.initialField;

        newChallengeEntries.points = parseInt(newChallengeEntries.points);
        await axios.post('http://localhost:8000/api/challenge/'+this.challenge.id+'/update', newChallengeEntries, {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer '+this.api_token,
            }
        }).then((response: any) => {
           this.$store.dispatch('getSessions')
           this.modifyLoading = false;
           this.modifyDialog = false;
        })
        .catch((err: any) => {console.log(JSON.stringify(err.message))});

    }

    private resetForm(): void {
        this.initialField = {
            description: this.challenge.description,
            points: this.challenge.points,
            title: this.challenge.title,
            user_id: this.$store.getters.currentUser.id
        }
    }



    private async challengeControl(action: string): Promise<void> {
        this.loading = true;

        if(action == 'validate')
        {
            const proofs = this.filesProof;
            if(proofs.length == 0)
            {
                this.loading = false;
                this.errorDialog = true;
                return
            }
        }

        const joinRes: any = await axios.post('http://localhost:8000/api/challenge/'+this.challenge.id+'/'+action, {user_id: this.user.id}, {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer '+this.api_token,
            }
        })
        .then(() => {
            if(action == 'validate') this.$store.dispatch('getValidatingChallenges');
            this.$store.dispatch('refreshCurrentUser');
            this.detailsDialog = false;
        }).catch((err: any) => {console.log(JSON.stringify(err.message))});
        this.loading = false;
    }
}
</script>
