<template>
    <v-main>
            <v-col sm="12">
                <v-card>


                    <v-tabs>
                        <v-tab
                                v-for="demo in demos"
                                :key="demo.id"
                                @click="changeStep(demo)"
                        >
                                {{demo.name}}
                        </v-tab>
                    </v-tabs>
                </v-card>
            </v-col>


            <v-col sm="12">
                <vue-simple-markdown :source="current.content"></vue-simple-markdown>
            </v-col>
    </v-main>
</template>

<script lang="ts">
import { Component, Vue} from 'vue-property-decorator'
import axios from 'axios';
import Demo from '../../Types/Demos.vue'


async function getDemos(): Promise<any>
{
    let demosData: any;
    let demos: any = [];
    await axios.get('http://localhost:8000/api/tutorials', {
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then((response: any) => demosData = response.data);

    demosData.forEach((demo: any) => {
        if(demo.status == 'active') demos.push(demo)
    });

    return demos;
}

@Component
export default class Demos extends Vue {

    private demosTable: any = [];

    private currentStep: any = {}

    private async created()
    {
        this.demosTable = await getDemos();
    }

    private get demos(): any { return this.demosTable }

    private get current(): any {return this.currentStep}

    private changeStep(demo: any) {
        this.currentStep = demo
    }


}
</script>
