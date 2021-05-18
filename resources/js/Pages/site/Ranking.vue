<template>
    <v-main>
       <v-row>
           <v-col sm="1" cols="12"></v-col>
           <v-col sm="10" cols="12">
               <h1 style="text-align:center;">Classement de la sessions en cours</h1>
                <v-text-field
                    v-model="search"
                    label="Rechercher par pseudo"
                    class="mx-4"
                ></v-text-field>
                <v-data-table :headers="headers" :search="search" :items="scores" class="elevation-1">
                    <template v-slot:item="row">
                        <tr>
                            <td>{{ row.item.user }}</td>
                            <td>{{ row.item.score }}</td>
                        </tr>
                    </template>
                </v-data-table>
           </v-col>
           <v-col sm="1" cols="12"></v-col>
       </v-row>
    </v-main>
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
    }).then((response: any) => scoresTab = response.data)

    return scoresTab;
}

@Component
export default class Ranking extends Vue {
    private scoresTab = [];

    private async created(): Promise<void> {
        this.scoresTab = await getScores()
    }

    private get scores()
    {
        return this.scoresTab;
    }

    private search = '';

     private headers = [
        { text: 'Participant', sortable: false, value: 'user'},
        { text: 'Score', sortable: false}
    ];

}
</script>
