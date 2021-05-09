<template>
    <v-container>
        <v-row>
            <v-col sm="8" cols="12">
                <h1>Bienvenue à toi ami joueur !</h1>
            </v-col>
            <v-col sm="4" cols="12" class="d-flex flex-column align-center">
                <h3>Nouvelles actus</h3>
                <div class="d-flex flex-wrap">
                    <v-col v-for="post in posts" :key="post.id">
                        <v-card
                            class="mx-auto"
                            max-width="200"
                        >
                            <v-img
                                class="white--text align-end"
                                height="100px"
                                src="https://picsum.photos/500/300?random=2"
                                >
                                <v-card-title>{{post.title}}</v-card-title>
                            </v-img>

                            <v-card-text class="text--primary">
                                <div>{{post.short_desc}}</div>
                            </v-card-text>

                            <v-card-actions>
                                <v-btn color="orange" text> Share </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </div>
            </v-col>
        </v-row>
    </v-container>
</template>

<script lang="ts">
import { Component, Vue, Prop } from 'vue-property-decorator';
import axios from 'axios';

async function getLastPosts(): Promise<any>
{
        let latestPosts: any;
        await axios.get('http://localhost:8000/api/posts/latest', {
            headers: {
                'Content-Type': 'application/json'
            }
        }).then((response) => {
            latestPosts = response.data.posts
        });

        return latestPosts;
}

@Component
export default class Home extends Vue {

    private latestPosts = {};

    private async created()
    {
        this.latestPosts = await getLastPosts();
    }

    private get posts(): any {
        return this.latestPosts;
    }

}
</script>

<style>


</style>
