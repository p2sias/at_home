<template>
<v-container>
    <v-row xs-6 id="login-row">
        <v-spacer></v-spacer>
        <v-col>
            <v-card>
                <v-toolbar
                    flat
                    color="blue"
                    dark
                >
                <v-toolbar-title>Veuillez vous connecter</v-toolbar-title>
                </v-toolbar>
                <v-card-text class="d-flex flex-column align-center">
                    <v-form
                        ref="form"
                        v-model="isValid"
                    >
                        <v-text-field
                            v-model="formData.email"
                            label="Adresse email"
                            :rules="emailRules"
                            required
                        ></v-text-field>

                        <v-text-field
                            v-model="formData.password"
                            label="Mot de passe"
                            type="password"
                            :rules="[v => !!v || 'Entrez un mot de passe']"
                            required
                        ></v-text-field>
                    </v-form>
                </v-card-text>
                <v-card-actions class="d-flex justify-center">
                    <v-btn v-if="!loading"
                        @click="auth()"
                        :disabled="!isValid"
                    >Login</v-btn>
                    <v-progress-circular v-if="loading"
                            indeterminate
                            color="primary"
                    ></v-progress-circular>
                </v-card-actions>
            </v-card>
        </v-col>
        <v-spacer></v-spacer>
    </v-row>
    <v-row justify="center">
        <v-dialog
        v-model="errorDialog"
        max-width="290"
        >
            <v-card>
                <v-card-title class="headline" color="red">Erreur</v-card-title>
                <v-card-text>Informations invalides</v-card-text>
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
    </v-row>
</v-container>

</template>

<script lang="ts">
/* eslint-disable  @typescript-eslint/no-explicit-any */
import { Component, Vue } from 'vue-property-decorator';
import axios from 'axios';

@Component({
   mounted() {
        //Lors de la création de la vue, si un utilisateur est déjà dans le store (connecté)
        //rediretion vers le dashboard
        if(this.$store.getters.currentUser != null) this.$router.push('/dashboard')
   }
})
export default class Login extends Vue {
    private isValid = true;
    private errorDialog = false;
    private loading = false;

    private formData = {
        email: '',
        password: '',
    };

    //Soumet une validaion du formulaire
    private validate (): void {
        (this.$refs.form as any).validate()
    }

    //Règes de validation de formulaire pour l'email
    private emailRules: any = [
        (v: string) => !!v || 'Entrez un e-mail',
        (v: string) => /.+@.+/.test(v) || 'E-mail non valide !'
    ];

    private async auth(): Promise<void> {
        this.loading = true;
        //Validation formulaire
        this.validate()
        if (!this.isValid) return

        //Envoi d'une requete de login sur l'API
        const authRes: any = await axios.post('http://localhost:8000/api/login', this.formData, {
            headers: {
                'Content-Type': 'application/json'
            }
        }).catch((err: any) => {console.log(JSON.stringify(err.message))});

        this.loading = false;

        //Si identifiants incorrects, alerte de l'user
        if(authRes.data.res.code == 401) this.errorDialog = true;
        else {
            const user: any = authRes.data.res.data;

            //Enregistrement de l'utilisateur connecté dans le store
            this.$store.dispatch('saveUser', user);
            //Redirection vers le dashboard
            this.$router.push('/dashboard');
        }
    }
}
</script>
