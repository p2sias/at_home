<template>
    <v-main>
        <v-card>
            <v-card-title>{{post.title}}</v-card-title>
            <v-card-actions>
                <v-btn v-if="!loading && isAdmin" @click="deletePost(post.id)">Supprimer</v-btn>
                <v-btn v-if="!loading && isAdmin" @click="editDialog=true">Modifier</v-btn>
                <v-btn v-if="!isAdmin" @click="editDialog=true">Voir</v-btn>
                <v-progress-circular v-if="loading"
                            indeterminate
                            color="primary"
                ></v-progress-circular>
            </v-card-actions>
        </v-card>

        <v-dialog v-model="editDialog">
            <PostEdit @taskEnded="taskEnded" action="modify" :post="post" :admin="isAdmin"/>
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
    {
        if(this.$store.getters.currentUser) {
            const user_lvl: any = this.$store.getters.currentUser.auth_level;
            if(user_lvl != 3 && user_lvl != 4 && user_lvl != 5) return false;
            return true;
        }
        return false;
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
        await axios.post('http://127.0.0.1:8000/api/post/'+post_id+'/delete', {user_id: this.$store.getters.currentUser.id}, {headers:
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
