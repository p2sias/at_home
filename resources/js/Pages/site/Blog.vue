<template>
    <v-container>
        <v-card>
            <v-card-title class="d-flex justify-space-between">
                <h1>Actualités @Home a Game</h1>
            </v-card-title>
            <v-col>
                <v-row>
                    <v-col cols="12" md="4" v-for="post in posts" :key="post.id">
                        <PostCard  :post="post" />
                    </v-col>
                </v-row>
            </v-col>
        </v-card>
    </v-container>
</template>

<script lang="ts">
import { Component, Vue} from 'vue-property-decorator'
import axios from 'axios'

import PostCard from '../../Components/blog/PostCard.vue'

async function getPosts(): Promise<any>
{
    let posts;
    await axios.get('http://127.0.0.1:8000/api/posts', {headers:{
        "Content-Type": "application/json"
    }}).then((response: any) => {
        posts = response.data;
    });

    console.log(posts);

    return posts;
}

@Component({
    components: {
        PostCard
    }
})
export default class Blog extends Vue {
    private postsTab: any = [];

    private async created()
    {
        this.postsTab = await getPosts();
    }

    private get posts()
    {
        return this.postsTab;
    }
}
</script>
