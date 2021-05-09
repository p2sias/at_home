<template>
    <v-main>
        <v-card v-if="isAdmin">
            <v-card-title>{{post.title}}</v-card-title>
            <v-card-actions>
                <v-btn v-if="!loading" @click="deletePost(post.id)">Supprimer</v-btn>
                <v-btn v-if="!loading" @click="editDialog=true">Modifier</v-btn>
                <v-progress-circular v-if="loading"
                            indeterminate
                            color="primary"
                ></v-progress-circular>
            </v-card-actions>
        </v-card>
        <v-card v-else>

        </v-card>

        <v-dialog v-model="editDialog">
            <PostEdit @taskEnded="taskEnded" action="modify" :post="post"/>
        </v-dialog>
    </v-main>
</template>

<script lang="ts">
import {Component, Vue, Prop} from 'vue-property-decorator';
import axios from 'axios';
import PostEdit from './PostEdit.vue'

@Component({
    components: {
        PostEdit
    }
})
export default class PostCard extends Vue {
    @Prop() readonly post: any;
    private editDialog = false;
    private loading = false;

    private get isAdmin(): boolean
    {   const user_lvl: any = this.$store.getters.currentUser.auth_level;
        if(user_lvl != 3 && user_lvl != 4) return false;
        return true;
    }

    private taskEnded(): void
    {
        this.$emit('refreshPosts');
        this.editDialog = false;
    }

    private async deletePost(post_id: number): Promise<void>
    {
        this.loading = true;
        const token = this.$store.getters.currentUser.api_token;
        await axios.get('http://127.0.0.1:8000/api/post/'+post_id+'/delete', {headers:
        {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        }}).then(() => {
            this.$emit('refreshPosts');
            this.loading = false;

        });

    }

}
</script>
