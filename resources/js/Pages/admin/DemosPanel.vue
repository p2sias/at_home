<template>
    <v-container>
        <v-card>
            <v-card-title class="d-flex justify-space-between">
                <h1>Liste des tutoriels</h1>
                <v-icon size="50" color="green" @click="editDialog=true">mdi-plus</v-icon>
            </v-card-title>
            <v-col>
                <v-row>
                    <v-col cols="12" md="4" v-for="demo in demos" :key="demo.id">
                        <DemoCard  :demo="demo" @refreshDemos="refreshDemos" />
                    </v-col>
                </v-row>
            </v-col>
        </v-card>
        <v-dialog v-model="editDialog">
            <DemoEdit @taskEnded="taskEnded" action="create"/>
        </v-dialog>
    </v-container>
</template>

<script lang="ts">
import { Component, Vue} from 'vue-property-decorator'
import axios from 'axios'
import DemoCard from '../../Components/demos/DemoCard.vue'
import DemoEdit from '../../Components/demos/DemoEdit.vue'


async function getDemos(): Promise<any>
{
    let demos: any;
    await axios.get('http://localhost:8000/api/tutorials', {
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then((response: any) => demos = response.data);

    return demos;
}

@Component({
    components: {
        DemoCard,
        DemoEdit
    }
})
export default class DemosPanel extends Vue {
    private demosTable: any = [];
    private current: any = {}
    private editDialog = false;

    private async created()
    {
        this.demosTable = await getDemos();
    }

    private get demos(): any { return this.demosTable }


    private async refreshDemos() {
        this.demosTable = await getDemos();
    }

    private taskEnded(): void
    {
        this.refreshDemos();
        this.editDialog = false;
    }
}
</script>
