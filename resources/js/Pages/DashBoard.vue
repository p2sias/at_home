<template>
    <v-container>
        <v-row  class="justify-center">
        <v-col v-if="!haveSession" class="sm-10">
            <v-card>
                <v-card-text class="d-flex flex-column align-center">
                    Vous n'avez pas encore rejoint de session
                </v-card-text>
                <v-card-actions class="d-flex justify-center">
                    <v-btn
                    to="/sessions"
                    >
                    Sessions
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-col>
        <v-col v-if="haveSession">
            <v-col sm="12" class="d-flex flex-wrap">
                <v-col cols="10"  sm="6">
                    <h2>Vos défis</h2>
                    <ChallengeDetails v-for="challenge in userChallenges" :key="challenge.id" :challenge="challenge" mode="current" />
                </v-col>

                <v-col cols="10"  sm="6">
                    <h2>Défis disponibles</h2>
                    <ChallengeDetails v-for="challenge in availableChallenges" :key="challenge.id" :challenge="challenge" mode="available" />
                </v-col>
            </v-col>

            <v-col sm="12">
                <h2>Vos validations</h2>
                <ChallengeDetails v-for="challenge in userValidations" :key="challenge.id" :challenge="challenge" mode="validating" />
            </v-col>
        </v-col>
    </v-row>
    </v-container>
</template>

<script lang="ts">
/* eslint-disable  @typescript-eslint/no-explicit-any */
import { Component, Vue } from 'vue-property-decorator';
import ChallengeDetails from '../Components/ChallengeDetails.vue'
import axios from 'axios';

@Component({
    components: {
        ChallengeDetails
    },
   mounted(){
       let user = this.$store.getters.currentUser
       if(user == null) this.$router.push('/');
       if(user.auth_level == 2 || user.auth_level == 3) this.$router.push('/admin/panel');
       if(user.auth_level == 4) this.$router.push('/admin/posts');
       //A chaque montage du dashboard on refresh l'utilisateur (pour les challenges) et les validations
       this.$store.dispatch('refreshCurrentUser');
       this.$store.dispatch('getValidations');
       this.$store.dispatch('getValidatingChallenges');
       this.$store.dispatch('getFilesByUser', user.id);

   }
})
export default class DashBoard extends Vue {
    private availableChallenges: any = [];

    private get haveSession(): boolean {

        if(this.$store.getters.userSession != null){

            let currentSession = this.session;

            //Tableaux d'objets en STR à des fins de comparaisons
            const userChallengesStr: any = [];
            const userValidatingStr: any = [];

            //On transforme tous les objets Challenges de l'user en string
            (this.userValidations).forEach((challenge: any) => {
                    userValidatingStr.push(JSON.stringify(challenge))
            });

            //On transforme tous les objets Validations de l'user en string
            (this.userChallenges).forEach((challenge: any) => {
                userChallengesStr.push(JSON.stringify(challenge))
            });


            let availableSort: any = [];

            //Pour chaques challenges de la session
            (currentSession.challenges).forEach((challenge: any) => {
                //Si ce challenge n'est ni rejoint, ni en cours de validation, alors on le fait appraitre dans les defis dissponnibles
                if(userChallengesStr.includes(JSON.stringify(challenge)) == false && userValidatingStr.includes(JSON.stringify(challenge)) == false) availableSort.push(challenge);
            });

            this.availableChallenges = availableSort;

            return true;
        }
        return false;
    }



    private get session(){
        return this.$store.getters.userSession;
    }

    private get availables(){
        this.haveSession;
        return this.availableChallenges;
    }

    private get userChallenges()
    {
        //Meme fonctionneent que pour HaveSession
        const validating = this.userValidations;
        const challenges = this.$store.getters.userChallenges;

        const userValidatingStr: any = [];

        validating.forEach((challenge: any) => {
                userValidatingStr.push(JSON.stringify(challenge))
        });

        let validatingSort: any = [];

        challenges.forEach((challenge: any) => {
            if(userValidatingStr.includes(JSON.stringify(challenge)) == false) validatingSort.push(challenge);
        });

        return validatingSort;
    }

    private get userValidations()
    {
        return this.$store.getters.userValidatingChallenges;
    }

    private get user(): any {
         return this.$store.getters.currentUser;
    }
}
</script>
