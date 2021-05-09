<template>
    <v-container>
        <v-card>
            <v-card-title class="d-flex justify-space-between">
                <h1>Liste des posts</h1>
                <v-icon size="50" color="green" @click="editDialog=true">mdi-plus</v-icon>
            </v-card-title>
            <v-col>
                <v-row>
                    <v-col cols="12" md="4" v-for="post in posts" :key="post.id">
                        <PostCard  :post="post" @refreshPosts="refreshPosts" />
                    </v-col>
                </v-row>
            </v-col>
        </v-card>
        <v-dialog v-model="editDialog">
            <PostEdit @taskEnded="taskEnded" action="create"/>
        </v-dialog>
    </v-container>
</template>

<script lang="ts">
/* eslint-disable  @typescript-eslint/no-explicit-any */
import { Component, Vue } from 'vue-property-decorator';
import axios from 'axios';
import PostCard from '../../Components/blog/PostCard.vue'
import PostEdit from '../../Components/blog/PostEdit.vue'

    async function getPosts(api_token: string): Promise<any>
    {
        let posts;
        await axios.get('http://127.0.0.1:8000/api/posts', {headers:{
            "Content-Type": "application/json",
            "Authorization": "Bearer "+api_token
        }}).then((response: any) => {
            posts = response.data;
        });

        return posts;
    }

@Component({
    components:{
        PostCard,
        PostEdit
    },
    mounted()
    {
        let user = this.$store.getters.currentUser
        if(user == null) this.$router.push('/');
        if(user.auth_level == 1) this.$router.push('/dashboard');
        if(user.auth_level == 2) this.$router.push('/admin/panel');


    }
})
export default class PostsPanel extends Vue {
    private user: any = this.$store.getters.currentUser;
    private postsList: any = [];
    private editDialog = false;


    private async created()
    {
        this.postsList = await getPosts(this.user.api_token);
    }

    private get posts(): any
    {
        return this.postsList;
    }

    private async refreshPosts(): Promise<void>
    {
        this.postsList = await getPosts(this.user.api_token);
    }

    private taskEnded(): void
    {
        this.refreshPosts();
        this.editDialog = false;
    }


}
</script>
