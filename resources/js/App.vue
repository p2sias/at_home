<template>
  <v-app>
    <v-app-bar
      max-height="120px"
      color="deep-purple"
      dark
    >
        <v-app-bar-nav-icon @click="drawer = true" class="hidden-md-and-up"></v-app-bar-nav-icon>
         <v-img
            style="margin-top:50px; cursor:pointer"
            max-width="250"
            src="https://zupimages.net/up/21/18/inko.png"
            @click="gohome"
        ></v-img>

        <v-spacer></v-spacer>

        <v-toolbar-items v-if="
            routeName == 'home' ||
            routeName == 'register' ||
            routeName == 'demos' ||
            routeName == 'login' ||
            routeName == 'blog' ||
            routeName == 'ranking'"
            class="hidden-sm-and-down">
            <v-btn  class="font-weight-bold" to="/login" color="white" text v-if="!logged">Se connecter</v-btn>
            <v-btn  class="font-weight-bold" to="/register" text v-if="!logged">S'inscrire</v-btn>
            <v-btn  class="font-weight-bold" to="/dashboard" text v-if="logged">Espace membre</v-btn>
            <v-btn  class="font-weight-bold" to="/blog" text>Actus</v-btn>
            <v-btn  class="font-weight-bold" to="/demos"  text>Démonstrations</v-btn>
            <v-btn  class="font-weight-bold" to="/ranking"  text>Classement</v-btn>
            <v-btn  class="font-weight-bold" v-if="logged" @click="logout()" color="black" text >Logout</v-btn>

        </v-toolbar-items>

        <v-toolbar-items v-if="
            routeName != 'home' &&
            routeName != 'register' &&
            routeName != 'demos' &&
            routeName != 'login' &&
            routeName != 'blog' &&
            routeName != 'ranking' &&
            logged && !simpleUser"
            class="hidden-sm-and-down">
            <v-btn  class="font-weight-bold" to="/admin/users" text v-if="isSuperAdmin">Utilisateurs</v-btn>
            <v-btn  class="font-weight-bold" to="/admin/demos" text v-if="isAdmin">Panel Tutoriels</v-btn>
            <v-btn  class="font-weight-bold" to="/admin/logs" text v-if="isSuperAdmin">Logs</v-btn>
            <v-btn  class="font-weight-bold" to="/admin/posts" text v-if="canPost || isSuperAdmin">Posts</v-btn>
            <v-btn  class="font-weight-bold" to="/admin/panel" text v-if="isAdmin">Panel administrateur</v-btn>
            <v-btn  class="font-weight-bold" to="/profile" text >Profil</v-btn>
            <v-btn  class="font-weight-bold" @click="logout()" color="black" text >Logout</v-btn>

        </v-toolbar-items>

        <v-toolbar-items v-if="
            routeName != 'home' &&
            routeName != 'register' &&
            routeName != 'demos' &&
            routeName != 'login' &&
            routeName != 'blog' &&
            routeName != 'ranking' &&
            logged && simpleUser"
            class="hidden-sm-and-down">
            <v-btn  class="font-weight-bold" to="/dashboard" text>Tableau de bord</v-btn>
            <v-btn  class="font-weight-bold" to="/sessions" text >Sessions</v-btn>
            <v-btn  class="font-weight-bold" to="/profile" text >Profil</v-btn>
            <v-btn  class="font-weight-bold" @click="logout()" color="black" text >Logout</v-btn>

        </v-toolbar-items>
    </v-app-bar>

    <v-navigation-drawer
      v-model="drawer"
      absolute
      temporary
    >
        <v-card v-if="logged">
            <v-card-title>
                <v-avatar
                    color="primary"
                    size="72"
                ></v-avatar>
                <v-icon @click="profilePage()">mdi-pencil</v-icon>
                <h1>{{currentUser.pseudo}}</h1>
            </v-card-title>
        </v-card>

      <v-list
        nav
        dense
      >
        <v-list-item-group
          v-model="group"
          class="deep-purple--text text--accent-4"
        >
          <div v-if="logged">
            <v-list-item v-if="simpleUser" to="/dashboard" @click="drawer = false">
                <v-list-item-icon>
                <v-icon>mdi-home</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Dashboard</v-list-item-title>
            </v-list-item>

            <v-list-item v-if="!hasSession  && simpleUser" to="/sessions" @click="drawer = false">
                <v-list-item-icon>
                <v-icon>mdi-account</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Sessions</v-list-item-title>
            </v-list-item>

            <v-list-item v-if="isSuperAdmin" to="/admin/users" @click="drawer = false">
                <v-list-item-icon>
                <v-icon>mdi-account-group</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Utilisateurs</v-list-item-title>
            </v-list-item>

            <v-list-item v-if="isSuperAdmin" to="/admin/demos" @click="drawer = false">
                <v-list-item-icon>
                <v-icon>mdi-account-group</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Panel Tutoriels</v-list-item-title>
            </v-list-item>

            <v-list-item v-if="isSuperAdmin" to="/admin/logs" @click="drawer = false">
                <v-list-item-icon>
                <v-icon>mdi-account-group</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Logs</v-list-item-title>
            </v-list-item>


            <v-list-item to="/admin/posts" @click="drawer = false" v-if="canPost || isSuperAdmin">
                <v-list-item-icon>
                <v-icon>mdi-post</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Posts</v-list-item-title>
            </v-list-item>

            <v-list-item v-if="!simpleUser && isAdmin" to="/admin/panel" @click="drawer = false">
                <v-list-item-icon>
                <v-icon>mdi-shield-account</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Panel administrateur</v-list-item-title>
            </v-list-item>

            <v-list-item @click="logout()">
                <v-list-item-icon>
                <v-icon>mdi-logout</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Log out</v-list-item-title>
            </v-list-item>
          </div>
        <v-divider v-if="logged"></v-divider>
        <div>
            <v-list-item to="/login" @click="drawer = false" v-if="!logged">
            <v-list-item-icon>
              <v-icon>mdi-login-variant</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Se connecter</v-list-item-title>
          </v-list-item>

          <v-list-item to="/register" @click="drawer = false" v-if="!logged">
            <v-list-item-icon>
              <v-icon>mdi-account-plus</v-icon>
            </v-list-item-icon>
            <v-list-item-title>S'inscrire</v-list-item-title>
          </v-list-item>

          <v-list-item to="/blog" @click="drawer = false">
            <v-list-item-icon>
              <v-icon>mdi-post</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Actualités</v-list-item-title>
          </v-list-item>

          <v-list-item to="/demos" @click="drawer = false">
            <v-list-item-icon>
              <v-icon>mdi-school</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Démonstrations</v-list-item-title>
          </v-list-item>

          <v-list-item to="/ranking" @click="drawer = false">
            <v-list-item-icon>
              <v-icon>mdi-format-list-numbered</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Classement</v-list-item-title>
          </v-list-item>
        </div>

        </v-list-item-group>
      </v-list>
    </v-navigation-drawer>



    <v-main>
      <router-view></router-view>
    </v-main>

    <v-footer height="auto" color="indigo" dark>
        <v-layout justify-center row wrap>
          <v-flex color="indigo" dark py-3 text-xs-center white--text xs12>
            &copy;2021 — <strong>At Home A Game</strong>
          </v-flex>
        </v-layout>
      </v-footer>
  </v-app>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import axios from 'axios';

@Component
export default class App extends Vue {
  private drawer = false;
  private group = null;

  private menu = [
      {key: 1, title: 'Se connecter', link: '/login', color: 'white'},
      {key: 2, title: 'S\'inscrire', link: '/register', color: 'white'},
      {key: 3, title: 'Actus', link: '/blog', color: 'black'},
      {key: 4, title: 'Démonstrations', link: '/demos', color: 'black'},
      {key: 5, title: 'Classement', link: '/ranking', color: 'black'}
  ]

  private get logged():boolean {
    if(this.$store.getters.currentUser != null) return true;
    return false;
  }

  private get isSuperAdmin(): boolean {
      if(this.currentUser.auth_level == 5) return true;
      return false;
  }

  private gohome(): void {
      this.$router.push({name: 'home'});
  }

  private get isAdmin(): boolean {
    if(this.$store.getters.currentUser.auth_level == 2 || this.$store.getters.currentUser.auth_level == 3 || this.$store.getters.currentUser.auth_level == 5) return true;
    return false;
  }

  private get simpleUser(): boolean {
    if(this.$store.getters.currentUser.auth_level == 1) return true;
    return false;
  }

  private get canPost(): boolean {
    if(this.$store.getters.currentUser.auth_level == 3 || this.$store.getters.currentUser.auth_level == 4) return true;
    return false;
  }

  private get hasSession(): boolean {
    if(this.$store.getters.currentSession == null) return false;
    return true;
  }

  private get currentUser(): any {
    return this.$store.getters.currentUser;
  }

  private profilePage(): void
  {
      this.$router.push('/profile');
  }

  private get routeName(): string
  {
      return this.$route.name;
  }

  private async logout(): Promise<void> {

    const logoutRes = await axios.post('http://localhost:8000/api/logout', {user_id: this.currentUser.id}, {headers:
    {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer '+ this.$store.getters.apiToken
    }});
    if(logoutRes.data.res.code == 200) {
      this.$store.dispatch('clearUser');
      this.$router.push('/login');
    }
  }
}
</script>
