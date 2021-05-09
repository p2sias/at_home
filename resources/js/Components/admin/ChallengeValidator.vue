<template>
    <v-container>
        <v-card class="d-flex justify-space-between  align-center">
            <v-card-title>Validation de {{user.pseudo}} | <span class="caption"> {{challenge.title}}</span></v-card-title>
            <v-card-actions>
                <v-btn v-if="validation.status == 'prog'" @click="validationDialog=true">...</v-btn>
                <v-btn v-else-if="validation.status == 'valid'" color="green" disabled>Validée</v-btn>
                <v-btn v-else color="red" disabled>Refusée</v-btn>
            </v-card-actions>
        </v-card>
        <v-dialog
        v-model="validationDialog"
        max-width="1000px"
        >
            <v-card
                
            >
                 <v-card-title>
                    <h2>Demande de validation</h2>
                </v-card-title>
                <v-col class="d-flex justify-space-between flex-wrap">
                    <v-col sm="6" cols="10">
                        <h3>Cette demande concerne le challenge suivant : </h3>
                        <v-card>
                            <v-card-title>{{challenge.title}}</v-card-title>
                            <v-card-text>
                                <p>{{challenge.description}}</p>
                                <span>Points : {{challenge.points}}</span>
                            </v-card-text>
                        </v-card>

                        <h3>Cette demande concerne l'utilisateur suivant :</h3>
                        <v-card>
                            <v-card-title>{{user.pseudo}}</v-card-title>
                            <div class="d-flex align-center justify-space-around">
                                <strong>{{user.name}}</strong>
                                <strong>{{user.surname}}</strong>
                                <strong>{{user.email}}</strong>
                            </div>
                        </v-card>

                        <v-col sm="6">
                            <h3>Preuves</h3>
                            <v-card  class="d-flex justify-space-between align-center" v-for="file in validation.files" :key="file.id">
                                <span>{{file.fileName}}</span>

                                <v-card-actions><v-icon @click="seeImage(file)">mdi-arrow-right-drop-circle</v-icon></v-card-actions>
                            </v-card>
                        </v-col>
                        
                    </v-col>

                    <v-col sm="6" cols="10">
                        <h3>Votre réponse</h3>
                        <v-card>
                            <v-form
                                ref="validationForm"
                            >
                                <v-textarea outlined label="Commentaire (obligatoire en cas de refus)" v-model="form.comment"></v-textarea>
                            </v-form>

                            <v-radio-group v-model="form.choice">
                                <v-radio
                                    label="Valider"
                                    value="valid"
                                ></v-radio>
                                <v-radio
                                    label="Refuser"
                                    value="reject"
                                ></v-radio>
                            </v-radio-group>

                            <v-card-actions class="d-flex justify-space-between">
                                <v-btn color="red" @click="validationDialog = false">Annuler</v-btn>
                                <v-btn v-if="!validateLoading" color="green" @click="sendResult()">Envoyer</v-btn>
                                <v-progress-circular v-else
                                        indeterminate
                                        color="primary"
                                ></v-progress-circular>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-col>
            </v-card>
        </v-dialog>
        <v-dialog
            v-model="displayImage"
            :max-width="image.imageWidth"
        >
            <v-card>
                <v-icon @click="displayImage = false">mdi-close</v-icon>
                <img :src="image.imageLink" :alt="image.imageType">
            </v-card>
        </v-dialog>
        <v-dialog
        v-model="errorDialog"
        max-width="290"
        >
            <v-card>
                <v-card-title class="headline" color="red">Erreur</v-card-title>
                <v-card-text>{{errorMessage}}</v-card-text>
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
    </v-container> 
</template>

<script lang="ts">
/* eslint-disable  @typescript-eslint/no-explicit-any */
import { Component, Vue, Prop } from 'vue-property-decorator';
import axios from 'axios';

@Component
export default class ChallengeValidator extends Vue {
    @Prop() readonly validation!: any;
    @Prop() readonly user!: any;
    @Prop() readonly challenge!: any;

    private form = {
        comment: '',
        choice: ''
    }

    private validationDialog = false;
    private errorDialog = false;

    private displayImage = false;
    private validateLoading = false;

    private api_token: string = this.$store.getters.apiToken;

    private imageData = {
        imageLink: '',
        imageType: '',
        imageWidth: '',
    }
    private errorMessage = '';

    private get image()
    {
        return this.imageData;
    }

    private seeImage(file: any)
    {
        this.imageData.imageLink = file.link;
        this.imageData.imageType = file.type;
        this.imageData.imageWidth = file.width;
        this.displayImage = true;
    }

    private async sendResult()
    {
        this.validateLoading = true;
        if(this.form.choice == 'reject' && this.form.comment == '')
        {
            this.errorMessage = 'En cas de refus, merci de préciser un commentaire';
            this.errorDialog = true;
        } else if (this.form.choice == '')
        {
            this.errorMessage = 'Merci d\'accepter ou refuser cette validation';
            this.errorDialog = true;
        } else {
            await axios.post('http://127.0.0.1:8000/api/validation/'+this.validation.id+'/validate', this.form, {
                headers: 
                {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer '+this.api_token,
                }
            }).then((response:any) => {
                if(response.data.res.code != 200)
                {
                    this.errorMessage = response.data.res.message;
                    this.errorDialog = true;
                }
                this.$store.dispatch('getAllValidationsBySessionId', this.$route.params.id);
                this.validationDialog = false;
            });
        }
        this.validateLoading = false; 
    }





}
</script>
