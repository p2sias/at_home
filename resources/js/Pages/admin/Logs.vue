<template>
    <v-main>
       <v-row>
           <v-col sm="1" cols="12"></v-col>
           <v-col sm="10" cols="12">
                <v-text-field
                    v-model="search"
                    label="Rechercher (pseudo, date)"
                    class="mx-4"
                ></v-text-field>
                <v-data-table :headers="headers" :search="search" :items="logs" class="elevation-1">
                    <template v-slot:item="row">
                        <tr>
                            <td>{{ row.item.message }}</td>
                            <td>{{ row.item.author }}</td>
                            <td>{{ row.item.created_at }}</td>
                            <td>{{ row.item.created_at_hour }}</td>
                        </tr>
                    </template>
                </v-data-table>
           </v-col>
           <v-col sm="1" cols="12"></v-col>
       </v-row>
    </v-main>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import axios from 'axios'

interface Log {
    id: number,
    message: string,
    author: string,
    created_at: string,
    created_at_hour: string
}

async function getLogs(token: string): Promise<any>
{
    let logs: any;
    await axios.get('http://localhost:8000/api/logs', {
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer '+ token
        }
    })
    .then((response: any) => logs = response.data);

    return logs;
}

@Component
export default class Logs extends Vue {

    private logsTable: Array<Log> = [];
    private search = '';

     private headers = [
        { text: 'Message', sortable: false},
        { text: 'Auteur', sortable: false, value: 'author'},
        { text: 'Date', sortable: false, value: 'created_at'},
        { text: 'Heure', sortable: false}
    ];

    private async created(): Promise<void>
    {
        this.logsTable = await getLogs(this.$store.getters.apiToken);
    }

    private get logs(): Array<Log> {
        return this.logsTable;
    }
}
</script>
