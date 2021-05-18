<template>
    <v-container>
         <v-row style="margin-top:50px;">
             <v-col sm="2" cols="12"></v-col>
             <v-col sm="8" cols="12"><UserDisplay @created="userCreated()" /></v-col>
            <v-col sm="2" cols="12"></v-col>

         </v-row>
    </v-container>
</template>

<script lang="ts">
/* eslint-disable  @typescript-eslint/no-explicit-any */
import { Component, Vue } from 'vue-property-decorator';
import axios from 'axios';
import UserDisplay from '../../Components/UserDisplay.vue'

@Component({
    components: {
        UserDisplay
    }
})
export default class Register extends Vue {
    private diag = true;

    private userCreated(): void {
        this.$router.push('/login');
    }
}
</script>
