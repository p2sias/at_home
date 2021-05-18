<template>
        <v-card>
            <v-card-title class="d-flex justify-space-between">
                <h3>{{act}} une démo</h3>
                <div>
                    <v-icon v-if="!modifyLoading" color="green" @click="saveChanges()">mdi-content-save</v-icon>
                    <v-icon v-if="!modifyLoading" color="red" @click="resetForm()">mdi-refresh</v-icon>
                    <v-icon v-if="!modifyLoading" color="red" @click="close()">mdi-close</v-icon>
                    <v-progress-circular
                        v-else
                        indeterminate
                        color="primary"
                    ></v-progress-circular>
                </div>
            </v-card-title>

            <v-card-text>
                    <v-form
                        ref="form"
                        v-model="isValid"
                    >
                        <div class="d-flex flex-wrap">
                            <v-text-field
                                v-model="formData.name"
                                label="Titre"
                                :rules="[v => !!v || 'Entrez un titre !']"
                                required
                            ></v-text-field>
                            <v-spacer></v-spacer>
                            <v-text-field
                                v-model="formData.order"
                                label="Ordre d'affichage"
                                :rules="[v => !!v || 'Entrez un ordre d\'affichage !']"
                                required
                            ></v-text-field>
                        </div>

                        <v-row>
                            <v-col md="6" cols="12">
                                <v-textarea
                                    v-model="formData.content"
                                    label="Texte en MarkDown"
                                    :rules="[v => !!v || 'Entrez un texte']"
                                    required
                                ></v-textarea>
                            </v-col>
                            <v-col md="6" cols="12">
                                <h3>Rendu</h3>
                                <vue-simple-markdown :source="formData.content"></vue-simple-markdown>
                            </v-col>
                        </v-row>


                    </v-form>
            </v-card-text>
        </v-card>
</template>

<script lang="ts">

import { Component, Vue, Prop} from 'vue-property-decorator'
import axios from 'axios'

@Component
export default class DemoEdit extends Vue {
    @Prop() readonly demo?: any
    @Prop() readonly action: string

    private modifyLoading = false;

    private isValid = true;

    private validate (): void {
        (this.$refs.form as any).validate()
    }

    private created()
    {
        if(this.demo != undefined)
        {
            this.formData = {
                name: this.demo.name,
                content: this.demo.content,
                status: this.demo.status,
                user_id: this.demo.user_id,
                order: this.demo.order
            }
        } else
        {
            this.formData = {
                name: '',
                content: '',
                status: 'active',
                user_id: this.$store.getters.currentUser.id,
                order: 0
            }
        }
    }

    private formData = {
        name: '',
        content: '',
        status: 'active',
        user_id: this.$store.getters.currentUser.id,
        order: 0
    }

    private get act(): string
    {
        if(this.action == 'modify') return 'Modifier'
        return 'Créer'
    }

    private get isEditing(): boolean
    {
        if(this.action == 'modify') return true;
        return false;
    }

    private resetForm(): void
    {
        if(this.demo == undefined)
        {
            this.formData = {
                name: '',
                content: '',
                status: 'active',
                user_id: this.$store.getters.currentUser.id,
                order: 0
            }
        } else {
            this.formData = {
                name: this.demo.name,
                content: this.demo.content,
                status: this.demo.status,
                user_id: this.demo.user_id,
                order: this.demo.order
            }
        }

    }

    private close(): void
    {
        this.$emit('taskEnded');
    }

    private async saveChanges(): Promise<void>
    {
        this.validate()
        if (!this.isValid) return

        if(this.action == 'create')
        {

            this.modifyLoading = true;
            let token = this.$store.getters.currentUser.api_token;
            await axios.post('http://127.0.0.1:8000/api/tutorial/create', this.formData, {headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer '+ token
            }}).then(() => {
                this.modifyLoading = false;
                this.resetForm();
                this.$emit('taskEnded');
            });
        } else {
            this.modifyLoading = true;
            let token = this.$store.getters.currentUser.api_token;
            this.formData.user_id = this.$store.getters.currentUser.id;
            await axios.post('http://127.0.0.1:8000/api/tutorial/'+this.demo.id+'/update', this.formData, {headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer '+ token
            }}).then(() => {
                this.modifyLoading = false;
                this.resetForm();
                this.$emit('taskEnded');
            });
        }

    }
}
</script>
