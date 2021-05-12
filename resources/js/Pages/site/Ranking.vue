<template>
    <v-container>

    </v-container>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import axios from 'axios';

async function getScores(): Promise<any> {
    let scoresTab: any;
    await axios.get('http://localhost:8000/api/scores', {
        headers : {
            'Content-Type' : 'application/json'
        }
    }
    )
}

@Component
export default class Ranking extends Vue {
    private scoresTab = [];

    private async created(): Promise<void> {
        this.$store.dispatch('getSessions');

        await getScores().then((response: any) => {
            const unsortedScores = response.data;

            unsortedScores.forEach((score:any) => {
                const session = this.$store.getters.getSessionById(score.session_id);
            })

        // a l'aide j'arrive pas a order by id je ne sais pas comment faire ALED KOOOOO ??? MOOO ????

        });

    }

    private get scores()
    {
        return this.scoresTab;
    }

}
</script>
