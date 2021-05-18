<template>
    <v-main>
        <v-card v-if="isAdmin">
             <v-card-title class="d-flex justify-space-between">
                <span>{{demo.name}}</span>
                <v-icon v-if="demo.status == 'active'" color="green">mdi-circle</v-icon>
                <v-icon v-else color="red">mdi-circle</v-icon>
            </v-card-title>
            <v-card-actions>
                <v-btn v-if="!loading" @click="deleteDemo(demo.id)">Supprimer</v-btn>
                <v-btn v-if="!loading" @click="editDialog=true">Modifier</v-btn>
                <v-btn v-if="!loading && demo.status == 'active'" @click="visibility('inactive')">Masquer</v-btn>
                <v-btn v-if="!loading && demo.status == 'inactive'" @click="visibility('active')">Afficher</v-btn>
                <v-progress-circular v-if="loading"
                            indeterminate
                            color="primary"
                ></v-progress-circular>
            </v-card-actions>
        </v-card>
        <v-card v-else>

        </v-card>

        <v-dialog v-model="editDialog">
            <DemoEdit @taskEnded="taskEnded" action="modify" :demo="demo"/>
        </v-dialog>
    </v-main>
</template>

<script lang="ts">
import {Component, Vue, Prop} from 'vue-property-decorator';
import axios from 'axios';
import DemoEdit from './DemoEdit.vue'

@Component({
    components: {
        DemoEdit
    }
})
export default class DemoCard extends Vue {
    @Prop() readonly demo: any;
    private editDialog = false;
    private loading = false;

    private get isAdmin(): boolean
    {   const user_lvl: any = this.$store.getters.currentUser.auth_level;
        if(user_lvl != 3 && user_lvl != 4 && user_lvl != 5) return false;
        return true;
    }

    private taskEnded(): void
    {
        this.$emit('refreshDemos');
        this.editDialog = false;
    }

    private async deleteDemo(demo_id: number): Promise<void>
    {
        this.loading = true;
        const token = this.$store.getters.currentUser.api_token;
        await axios.post('http://127.0.0.1:8000/api/tutorial/'+demo_id+'/delete', {user_id: this.$store.getters.currentUser.id}, {headers:
        {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        }}).then(() => {
            this.$emit('refreshDemos');
            this.loading = false;

        });

    }

    private async visibility(action: string): Promise<void>
    {
        this.loading = true;
        const token = this.$store.getters.currentUser.api_token;
        await axios.post('http://127.0.0.1:8000/api/tutorial/'+this.demo.id+'/visibility', {action: action, user_id: this.$store.getters.currentUser.id}, {headers:
        {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        }}).then(() => {
            this.$emit('refreshDemos');
            this.loading = false;

        });
    }

}
</script>
