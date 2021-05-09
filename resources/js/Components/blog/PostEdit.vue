<template>
        <v-card>
            <v-card-title class="d-flex justify-space-between">
                <h3>{{act}} un post</h3>
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
                        <v-text-field
                            v-model="formData.title"
                            label="Titre"
                            :rules="[v => !!v || 'Entrez un titre !']"
                            required
                        ></v-text-field>

                        <v-text-field
                            v-model="formData.short_desc"
                            label="Description"
                            :rules="[v => !!v || 'Entrez une description']"
                            required
                        ></v-text-field>

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
export default class PostEdit extends Vue {
    @Prop() readonly post?: any
    @Prop() readonly action: string

    private modifyLoading = false;

    private isValid = true;

    private validate (): void {
        (this.$refs.form as any).validate()
    }

    private created()
    {
        if(this.post != undefined)
        {
            this.formData = {
                title: this.post.title,
                content: this.post.content,
                short_desc: this.post.short_desc,
                user_id: this.post.user_id
            }
        } else
        {
            this.formData = {
                title: '',
                content: '',
                short_desc: '',
                user_id: this.$store.getters.currentUser.id
            }
        }
    }

    private formData = {
        title: '',
        content: '',
        short_desc: '',
        user_id: this.$store.getters.currentUser.id
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
        if(this.post == undefined)
        {
            this.formData = {
                title: '',
                content: '',
                short_desc: '',
                user_id: this.$store.getters.currentUser.id
            }
        } else {
            this.formData = {
                title: this.post.title,
                content: this.post.content,
                short_desc: this.post.short_desc,
                user_id: this.post.user_id
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
            await axios.post('http://127.0.0.1:8000/api/post/create', this.formData, {headers: {
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
            await axios.post('http://127.0.0.1:8000/api/post/'+this.post.id+'/update', this.formData, {headers: {
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
