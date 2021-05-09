<template>
    <v-container>
        <v-row>
            <h1>Profil de {{user.pseudo}}</h1>
        </v-row>

        <v-row>
            <v-col sm="6" cols="12">
                <v-card>
                    <v-card-title class="d-flex justify-space-between">
                        <h2>Vos informations</h2>
                        <div>
                            <v-btn v-if="!modifyLoading" @click="saveUser()" color="green">Save</v-btn>
                            <v-btn v-if="!modifyLoading" color="red" @click="updateField()">Reset</v-btn>
                            <v-progress-circular v-else
                                        indeterminate
                                        color="primary"
                            ></v-progress-circular>
                        </div>
                    </v-card-title>
                    <v-col>
                        <v-form
                        v-model="isUserFormValid"
                        ref="userForm"
                        >
                            <v-col class="d-flex justify-space-between flex-wrap">
                                <v-text-field
                                    v-model="formData.name"
                                    label="Nom"
                                    required
                                    :rules="[v => !!v || 'Entrez un nom']"
                                ></v-text-field>

                                <v-spacer></v-spacer>

                                <v-text-field
                                    v-model="formData.surname"
                                    label="Prénom"
                                    required
                                    :rules="[v => !!v || 'Entrez un prénom']"
                                ></v-text-field>
                            </v-col>

                            <div class="d-flex ">
                                <v-text-field
                                    v-model="formData.pseudo"
                                    label="Pseudo"
                                    required
                                    @change="checkUnique('pseudo')"
                                    :rules="[v => !!v || 'Entrez un pseudo']"
                                ></v-text-field>
                                <v-spacer></v-spacer>
                                <v-icon v-if="uniquePseudo && !uniquePseudoCheckLoading" color="green">mdi-check-bold</v-icon>
                                <v-icon v-if="!uniquePseudo && !uniquePseudoCheckLoading" color="red">mdi-close-circle</v-icon>
                                <v-progress-circular v-if="uniquePseudoCheckLoading"
                                        indeterminate
                                        color="primary"
                                ></v-progress-circular>
                            </div>


                           <div class="d-flex ">
                                <v-text-field
                                    v-model="formData.email"
                                    label="Adresse email"
                                    required
                                    :rules="emailRules"
                                    @change="checkUnique('email')"
                                ></v-text-field>
                                <v-spacer></v-spacer>
                                <v-icon v-if="uniqueEmail && !uniqueEmailCheckLoading" color="green">mdi-check-bold</v-icon>
                                <v-icon v-if="!uniqueEmail && !uniqueEmailCheckLoading" color="red">mdi-close-circle</v-icon>
                                <v-progress-circular v-if="uniqueEmailCheckLoading"
                                        indeterminate
                                        color="primary"
                                ></v-progress-circular>
                            </div>

                            <v-menu
                                    ref="birthMenu"
                                    v-model="birthday"
                                    :close-on-content-click="false"
                                    :return-value.sync="formData.birthday"
                                    transition="scale-transition"
                                    offset-y
                                    min-width="auto"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="formData.birthday"
                                        label="Start date"
                                        prepend-icon="mdi-calendar"
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                    ></v-text-field>
                                    </template>
                                    <v-date-picker
                                    v-model="formData.birthday"
                                    no-title
                                    scrollable
                                    :max="new Date().toISOString().substr(0, 10)"
                                    >
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="$refs.birthMenu.save(formData.birthday)"
                                    >
                                        OK
                                    </v-btn>
                                    </v-date-picker>
                                </v-menu>

                            <div class="d-flex ">
                                <v-text-field
                                    v-model="formData.phone"
                                    label="Numéro de téléphone"
                                    required
                                    @change="checkUnique('phone')"
                                    :rules="[v => !!v || 'Entrez un numéro de téléphone']"
                                ></v-text-field>
                                <v-spacer></v-spacer>
                                <v-icon v-if="uniquePhone && !uniquePhoneCheckLoading" color="green">mdi-check-bold</v-icon>
                                <v-icon v-if="!uniquePhone && !uniquePhoneCheckLoading" color="red">mdi-close-circle</v-icon>
                                <v-progress-circular v-if="uniquePhoneCheckLoading"
                                        indeterminate
                                        color="primary"
                                ></v-progress-circular>
                            </div>

                            <v-text-field
                                v-model="formData.address"
                                label="Adresse postale"
                                :rules="[v => !!v || 'Entrez une adresse postale']"
                                required
                            ></v-text-field>

                            <v-text-field
                                v-model="formData.postal_code"
                                label="Code postal"
                                required
                                :rules="[v => !!v || 'Entrez un code postal']"
                            ></v-text-field>

                            <v-text-field
                                v-model="formData.city"
                                label="Ville"
                                required
                                :rules="[v => !!v || 'Entrez une ville']"
                            ></v-text-field>
                        </v-form>
                    </v-col>
                </v-card>
            </v-col>
            <v-col sm="6" cols="12">
                <v-col>
                    <v-card>
                        <v-card-title class="d-flex justify-space-between">
                            <h2>Modifier mon mot de passe</h2>
                            <v-btn v-if="!passLoading" color="green" @click="savePass()">Save</v-btn>
                            <v-progress-circular v-else
                                        indeterminate
                                        color="primary"
                            ></v-progress-circular>
                        </v-card-title>
                        <v-col>
                            <v-form
                            v-model="passFormValid"
                            ref="passForm"
                            >
                                <v-text-field
                                    v-model="resetPassData.current"
                                    label="Mot de passe actuel"
                                    type="password"
                                    :rules="[v => !!v || 'Entrez votre mot de passe']"
                                    required
                                ></v-text-field>

                                <v-text-field
                                        v-model="resetPassData.new"
                                        label="Nouveau mot de passe"
                                        type="password"
                                        :rules="[v => !!v || 'Entrez un nouveau mot de passe']"
                                        required
                                ></v-text-field>
                            </v-form>
                        </v-col>
                    </v-card>
                </v-col>
            </v-col>
        </v-row>

        <v-row justify="center">
            <v-dialog
            v-model="dialog"
            max-width="290"
            >
                <v-card>
                    <v-card-title class="headline" color="red">{{dialogData.title}}</v-card-title>
                    <v-card-text>{{dialogData.content}}</v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="red"
                            text
                            @click="dialog = false"
                        > Ok </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-row>
    </v-container>
</template>

<script lang="ts">
/* eslint-disable  @typescript-eslint/no-explicit-any */
import { Component, Vue } from 'vue-property-decorator';
import axios from 'axios';

@Component
export default class ProfilePage extends Vue {

    private uniquePseudoCheckLoading = false;
    private uniqueEmailCheckLoading = false;
    private uniquePhoneCheckLoading = false;



    private uniquePseudo = true;
    private uniqueEmail = true;
    private uniquePhone = true;

    private birthday = false;

    private passFormValid = true;

    private dialog = false;

    private dialogData = {
        title: '',
        content: '',
    }


    private mounted()
    {
        this.updateField();
    }

    private modifyLoading = false;

    //Règes de validation de formulaire pour l'email
    private emailRules: any = [
        (v: string) => !!v || 'Entrez un e-mail',
        (v: string) => /.+@.+/.test(v) || 'E-mail non valide !'
    ];

    private isUserFormValid = true;
    private formData = {
        name: '',
        surname: '',
        pseudo: '',
        email: '',
        birthday: '',
        phone: '',
        address: '',
        postal_code: '',
        city: ''
    }

    private resetPassData = {
        current: '',
        new: '',
    }

    private updateField(): void
    {
        this.formData = {
            name: this.user.name,
            surname: this.user.surname,
            pseudo: this.user.pseudo,
            email: this.user.email,
            birthday: this.user.birthday,
            phone: this.user.phone,
            address: this.user.address,
            postal_code: this.user.postal_code,
            city: this.user.city
        }

        this.uniquePseudo = true;
        this.uniqueEmail = true;
        this.uniquePhone = true;
    }



    private get user(): any
    {
        return this.$store.getters.currentUser;
    }

    private validateUser (): void {
        (this.$refs.userForm as any).validate()
    }

    private validatePass (): void {
        (this.$refs.passForm as any).validate()
    }

    private async checkUnique(type: string): Promise<void>
    {
        let check: string;
        if(type == 'pseudo'){
            check = this.formData.pseudo;
            this.uniquePseudoCheckLoading = true;
        }
        if(type == 'email'){
            check = this.formData.email;
            this.uniqueEmailCheckLoading = true;
        }
        if(type == 'phone'){
            check = this.formData.phone;
            this.uniquePhoneCheckLoading = true;

        }

        //Envoi d'une requete de login sur l'API
        const authRes: any = await axios.post('http://localhost:8000/api/user/checkUnique', {field: type, valueToCheck: check, currentUserId: this.user.id}, {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer '+this.user.api_token
            }
        })
        .then((response: any) => {
            let exist = response.data.exist;

            if(type == 'pseudo')
            {
                if(exist) this.uniquePseudo = false;
                else this.uniquePseudo = true;
            }

            if(type == 'email')
            {
                if(exist) this.uniqueEmail = false;
                else this.uniqueEmail = true;
            }

            if(type == 'phone')
            {
                if(exist) this.uniquePhone = false;
                else this.uniquePhone = true;
            }

            this.uniquePseudoCheckLoading = false;
            this.uniqueEmailCheckLoading = false;
            this.uniquePhoneCheckLoading = false;
        })
        .catch((err: any) => {console.log(JSON.stringify(err.message))});
    }

    private async saveUser(): Promise<void>
    {
        if(this.uniquePseudo && this.uniqueEmail && this.uniquePhone)
        {
             this.modifyLoading = true;
            //Validation formulaire
            this.validateUser()
            if (!this.isUserFormValid) return

            //Envoi d'une requete de login sur l'API
            const authRes: any = await axios.post('http://localhost:8000/api/user/'+this.user.id+'/update', this.formData, {
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer '+this.user.api_token
                }
            })
            .then(async (response) => {
                await this.$store.dispatch('refreshCurrentUser');
                this.modifyLoading = false;
            })
            .catch((err: any) => {console.log(JSON.stringify(err.message))});

            this.updateField();
        }
    }

    private passLoading = false;
    private async savePass(): Promise<void>
    {
        this.validatePass()
        if (!this.passFormValid) return

        this.passLoading = true;
        //Envoi d'une requete de login sur l'API
        const authRes: any = await axios.post('http://localhost:8000/api/user/'+this.user.id+'/changePassword', this.resetPassData, {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer '+this.user.api_token
            }
        })
        .then(async (response) => {
            let res = response.data;

            if(res.changed)
            {
                this.dialogData.title = 'Confirmation';
                this.dialogData.content = 'Votre mot de passe à bien été changé !';
                this.dialog = true;


            } else {
                this.dialogData.title = 'Erreur';
                this.dialogData.content = 'Mot de passe actuel invalide';
                this.dialog = true;
            }

            this.resetPassData.current = '',
            this.resetPassData.new = '',
            this.passLoading = false;
        })
        .catch((err: any) => {console.log(JSON.stringify(err.message))});

    }
}
</script>
