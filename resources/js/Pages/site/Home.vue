<template>
    <v-container>
        <v-row>
            <v-col sm="8" cols="12">
                <h1>Bienvenue à toi ami joueur !</h1>
                <p>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime mollitia,
                    molestiae quas vel sint commodi repudiandae consequuntur voluptatum laborum
                    numquam blanditiis harum quisquam eius sed odit fugiat iusto fuga praesentium
                    optio, eaque rerum! Provident similique accusantium nemo autem. Veritatis
                    obcaecati tenetur iure eius earum ut molestias architecto voluptate aliquam
                    nihil, eveniet aliquid culpa officia aut! Impedit sit sunt quaerat, odit,
                    tenetur error, harum nesciunt ipsum debitis quas aliquid. Reprehenderit,
                    quia. Quo neque error repudiandae fuga? Ipsa laudantium molestias eos
                    sapiente officiis modi at sunt excepturi expedita sint? Sed quibusdam
                    recusandae alias error harum maxime adipisci amet laborum. Perspiciatis
                    minima nesciunt dolorem! Officiis iure rerum voluptates a cumque velit
                    quibusdam sed amet tempora. Sit laborum ab, eius fugit doloribus tenetur
                    fugiat, temporibus enim commodi iusto libero magni deleniti quod quam
                    consequuntur! Commodi minima excepturi repudiandae velit hic maxime
                    doloremque. Quaerat provident commodi consectetur veniam similique ad
                    earum omnis ipsum saepe, voluptas, hic voluptates pariatur est explicabo
                    fugiat, dolorum eligendi quam cupiditate excepturi mollitia maiores labore
                    suscipit quas? Nulla, placeat. Voluptatem quaerat non architecto ab laudantium
                    modi minima sunt esse temporibus sint culpa, recusandae aliquam numquam
                    totam ratione voluptas quod exercitationem fuga. Possimus quis earum veniam
                    quasi aliquam eligendi, placeat qui corporis!
                </p>
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
                                <v-btn color="orange" to="/blog" text> Voir </v-btn>
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
