<template>
    <v-row justify="center">
        <v-dialog
        v-model="confimDialog"
        max-width="290"
        >
            <v-card v-if="!confirmed">
                <v-card-title class="headline">{{selected.title}}</v-card-title>
                <v-card-text>Voulez vous rejoindre cette session ?</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="red"
                        text
                        @click="cancel()"
                    > Non </v-btn>
                    <v-btn
                        color="green"
                        text
                        @click="validate()"
                    > Oui </v-btn>
                </v-card-actions>
            </v-card>
            <v-card v-else>
                <v-toolbar
                    flat
                    color="blue"
                    dark
                >
                <v-toolbar-title>Procédez au paiement</v-toolbar-title>
                </v-toolbar>
                <v-card-text class="d-flex flex-column align-center">
                    <v-form
                        ref="form"
                    >
                        <v-text-field
                            v-model="formData.cardName"
                            label="Nom sur la carte"
                        ></v-text-field>

                        <v-text-field
                            v-model="formData.cardNumber"
                            label="Numéro de la carte"
                        ></v-text-field>

                        <v-text-field
                            v-model="formData.expDate"
                            label="Numéro de la carte"
                        ></v-text-field>

                        <v-text-field
                            v-model="formData.cardCrypt"
                            label="Numéro de la carte"
                        ></v-text-field>
                    </v-form>
                </v-card-text>
                <v-card-actions class="d-flex justify-center">
                    <v-btn
                        @click="pay()"
                    >Payer 50€</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>

<script lang="ts">
import { Component, Vue, Prop } from 'vue-property-decorator';
import axios from 'axios';

@Component
export default class DashBoard extends Vue {
    @Prop() readonly selected!: any

    private formData = {
        cardName: '',
        cardNumber: '',
        expDate: '',
        cardCrypt: '' 
    }

    private confimDialog = true;

    private confirmed = false;

    private cancel(): void {
        this.$emit('cancel')
    }

    private validate(): void {
        this.confirmed = true;
    }

    private async pay()
    {   
        const user = this.$store.getters.currentUser;

        const headers = {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + this.$store.getters.apiToken
        }
        const joinSessionRes = await axios.post('http://localhost:8000/api/session/'+ this.selected.id + '/join', {user_id: user.id}, {headers}).then(response => {
            this.$store.dispatch('refreshCurrentUser');
            this.$router.push('/dashboard');
        })

        


    }
    


}
</script>