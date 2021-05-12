<template>

                <v-card>
                    <v-card-title class="d-flex justify-space-between">
                        <h2>Création utilisateur</h2>
                        <div>
                            <v-btn v-if="!modifyLoading" @click="saveUser()" color="green">Save</v-btn>
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

                            <v-text-field
                                    v-model="formData.password"
                                    label="Mot de passe"
                                    type="password"
                                    :rules="[v => !!v || 'Entrez votre mot de passe']"
                                    required
                            ></v-text-field>

                            <v-text-field
                                    label="Confirmation de mot de passe"
                                    type="password"
                                    :rules="confirmPasswordRules"
                                    required
                            ></v-text-field>

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
</template>

<script lang="ts">

import {Component, Vue} from 'vue-property-decorator';
import axios from 'axios';

@Component
export default class UserDisplay extends Vue {

    private uniquePseudoCheckLoading = false;
    private uniqueEmailCheckLoading = false;
    private uniquePhoneCheckLoading = false;

    private uniquePseudo = true;
    private uniqueEmail = true;
    private uniquePhone = true;

    private get user(): any
    {
        return this.$store.getters.currentUser;
    }

    private birthday = false;

    private modifyLoading = false;

    //Règes de validation de formulaire pour l'email
    private emailRules: any = [
        (v: string) => !!v || 'Entrez un e-mail',
        (v: string) => /.+@.+/.test(v) || 'E-mail non valide !'
    ];

    private confirmPasswordRules: any = [
        (confirmPassword: string) =>
        confirmPassword === this.formData.password ||
        "Les mots de passes doivent correspondre"
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
        city: '',
        password: ''
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

    private validateUser (): void {
        (this.$refs.userForm as any).validate()
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
            const authRes: any = await axios.post('http://localhost:8000/api/user/store', this.formData, {
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(async (response) => {
                this.modifyLoading = false;
                this.$emit('created');
            })
            .catch((err: any) => {console.log(JSON.stringify(err.message))});

            this.formData = {
                name: '',
                surname: '',
                pseudo: '',
                email: '',
                birthday: '',
                phone: '',
                address: '',
                postal_code: '',
                city: '',
                password: ''
            }
        }
    }
}


</script>
