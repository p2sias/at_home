<template>
    <v-container>
        <v-row>
            <v-col class="d-flex flex-column">
                <div class="d-flex justify-space-between">
                    <v-text-field
                        v-model="search"
                        label="Rechercher (pseudo, email)"
                        class="mx-4"
                    ></v-text-field>
                    <v-icon @click="createDialog = true">mdi-plus</v-icon>
                </div>

                <v-data-table :headers="headers" :search="search" :items="users" dense class="elevation-1">
                    <template v-slot:item="row">
                        <tr>
                            <td>{{ row.item.name }}</td>
                            <td>{{ row.item.surname }}</td>
                            <td>{{ row.item.pseudo }}</td>
                            <td>{{ row.item.email }}</td>
                            <td v-if="row.item.pseudo != user.pseudo">
                                <v-select
                                    v-model="row.item.auth_level"
                                    :items="authorizationsLvl"
                                    item-text="role"
                                    item-value="auth_lvl"
                                    label="select"
                                    solo
                                    @change="changeRole(row.item.id, row.item.auth_level, row.item.pseudo)"
                                ></v-select>
                            </td>
                            <td v-else>
                               <v-select
                                    v-model="row.item.auth_level"
                                    :items="authorizationsLvl"
                                    item-text="role"
                                    item-value="auth_lvl"
                                    label="select"
                                    solo
                                    disabled
                                    @change="changeRole(row.item.id, row.item.auth_level, row.item.pseudo)"
                                ></v-select>
                            </td>
                        </tr>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
        <v-snackbar
        v-model="snackbar"
        timeout="3000"
        >
        {{ text }}

            <template v-slot:action="{ attrs }">
                <v-btn
                color="blue"
                text
                v-bind="attrs"
                @click="snackbar = false"
                >
                Fermer
                </v-btn>
            </template>
        </v-snackbar>

        <v-dialog
            v-model="createDialog"
            max-width="600px"
        >
            <UserDisplay @created="userCreated()" />
        </v-dialog>
    </v-container>
</template>

<script lang="ts">
/* eslint-disable  @typescript-eslint/no-explicit-any */
import { Component, Vue } from "vue-property-decorator";
import UserDisplay from "../../Components/UserDisplay.vue"
import axios from "axios";
import store from "../../Store";

async function getUsers(): Promise<any> {
    let users: any;
    await axios
        .get("http://localhost:8000/api/users", {
            headers: {
                "Content-Type": "application/json",
                Authorization: "Bearer " + store.getters.apiToken
            }
        })
        .then((response: any) => {
            users = response.data;
        });
    return users;
}

@Component({
    components: {
        UserDisplay
    }
})
export default class Users extends Vue {
    private usersTab: any = [];

    private search = '';
    private text = '';
    private snackbar = false;

    private roleSelect = 0;

    private createDialog = false;

    private headers = [
        { text: 'Prénom', sortable: false},
        { text: 'Nom', sortable: false},
        { text: 'Pseudo', sortable: false, value: 'pseudo'},
        { text: 'Email', sortable: false, value: 'email'}
    ];

    private async created() {
        if(this.$store.getters.currentUser.auth_level != 5) this.$router.push('/')
        else {
            await getUsers().then((response: any) => {
            console.log(response);
            response.forEach((user: any) => {
                this.usersTab.push({
                    id: user.id,
                    name: user.name,
                    surname: user.surname,
                    pseudo: user.pseudo,
                    email: user.email,
                    auth_level: user.auth_level
                });

            });
        });
        }
    }

    private get user(): void {
        return this.$store.getters.currentUser;
    }

    private async userCreated(): Promise<void>
    {
        this.createDialog = false;
        this.usersTab = await getUsers();
    }

    private get users(): any {
        return this.usersTab;
    }

    private authorizationsLvl = [
        {auth_lvl: 1, role: 'Participant'},
        {auth_lvl: 2, role: 'Administrateur'},
        {auth_lvl: 3, role: 'Administrateur - Blog'},
        {auth_lvl: 4, role: 'Blog'},
        {auth_lvl: 5, role: 'Super Admin'}
    ]

    private async changeRole(id: number, auth_lvl: number, pseudo: string): Promise<void> {
        console.log('Change role for user id: '+id +' to '+auth_lvl);

        await axios
        .post("http://localhost:8000/api/user/"+id+"/changeAuth/"+auth_lvl, {security_id: this.$store.getters.currentUser.id}, {
            headers: {
                "Content-Type": "application/json",
                Authorization: "Bearer " + store.getters.apiToken
            }
        })
        .then( async (response: any) => {
            if(response.data != 403)
            {
                this.usersTab = await getUsers();
                this.text = `Les droits de l'utilisateur ${pseudo} ont bien été modifiés`

            } else  this.text = `Vous n'etes pas autorisé à faire cela !`
            this.snackbar = true;
        });

    }
}
</script>
