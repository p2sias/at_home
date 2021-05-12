<template>
    <v-content>
        <v-row xs-6 class="justify-center">
            <v-col v-if="!haveSession" sm="6" cols="12">
                <v-card class="d-flex justify-space-between" v-for="session in sessions" :key="session.id">
                        <div>
                            <v-card-title>{{session.title}}</v-card-title>
                            <v-card-text class="d-flex flex-column">
                                <span>Démarre le {{session.start_date}}</span>
                                <span>Termine le {{session.end_date}}</span>
                            </v-card-text>
                        </div>
                        <div class="d-flex align-center">
                            <v-card-actions class="d-flex align-center" pa="3">
                                <v-btn
                                    v-if="session.status == 'ofj'"
                                    color="green"
                                    @click="selectSession(session)"
                                >
                                    Rejoindre
                                </v-btn>
                                <v-btn
                                    v-else-if="session.status == 'started'"
                                    disabled
                                    color="blue"
                                >
                                    En cours
                                </v-btn>
                                <v-btn
                                    v-else-if="session.status == 'full'"
                                    disabled
                                >
                                    Pleine
                                </v-btn>
                            </v-card-actions>
                        </div>
                </v-card>
            </v-col>
            <v-col v-if="!haveSession" sm="6" cols="12">
                    <h2>Règles et modalités</h2>
                    <div>
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Labore sint quasi deserunt ullam! Natus voluptatem, sapiente debitis obcaecati veritatis necessitatibus exercitationem tempora reiciendis velit aspernatur! Officia rerum, temporibus praesentium alias consequatur quaerat ut similique est pariatur dolores, soluta porro veritatis commodi, corrupti ab nisi minima! Dolorem, debitis fuga expedita saepe aperiam voluptatem illum illo deserunt necessitatibus sit facere explicabo possimus vitae aut aliquam optio doloremque labore numquam enim eum sed unde quas? Libero ex fuga odit exercitationem beatae laboriosam deleniti minima, voluptatum magnam unde adipisci sint quasi soluta quis dolor tempore eveniet explicabo! Rem accusamus mollitia tempore perferendis incidunt libero?
                    </div>
            </v-col>
            <v-col v-if="haveSession" class="sm-10">
                <v-card>
                    <v-card-text class="d-flex flex-column align-center">
                        Vous participez déjà à une session !
                    </v-card-text>
                    <v-card-actions class="d-flex justify-center">
                        <v-btn
                        to="/dashboard"
                        >
                        Dashboard
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
        <SessionJoin v-if="selected" :selected="selectedSession" @cancel="selected = false; selectedSession = {}" />
    </v-content>
</template>

<script lang="ts">
/* eslint-disable  @typescript-eslint/no-explicit-any */
import { Component, Vue } from 'vue-property-decorator';
import SessionJoin from '../Components/SessionJoin.vue';

@Component({
   created(){
       let user = this.$store.getters.currentUser
       if(user == null) this.$router.push('/');
       if(user.auth_level == 2 || user.auth_level == 3) this.$router.push('/admin/panel');
       if(user.auth_level == 4) this.$router.push('/admin/posts');
       this.$store.dispatch('getSessions');

   },
   components: {
       SessionJoin
   }
})
export default class Session extends Vue {
    private haveSession = false;
    private selectedSession: any = {}
    private selected = false;

    private mounted(){
        if(this.$store.getters.userSession != null) this.haveSession = true;
    }

    get sessions(): any {
        return this.$store.getters.allSessions;
    }

    private selectSession(session: any): void{
        this.selectedSession = session;
        this.selected = true;
    }
}
</script>
