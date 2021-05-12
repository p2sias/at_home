import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'
import axios from 'axios';
import store from './index';

Vue.use(Vuex);

/* eslint-disable  @typescript-eslint/no-explicit-any */

export default new Vuex.Store({
  plugins: [createPersistedState()],
  state: {
    user: null,
    sessions: null,
    challenges: null,
    validationsFiles: null,
    validatingChallenges: null,
    userValidations: null,
    validationsBySession: null,
    // loadings
    loadingValidations: false,
  },
  mutations: {
    SET_USER(state: any, user: any)
    {
      state.user = user;
    },
    CLEAR_USER(state: any)
    {
        state.user = null;
        state.sessions = null;
        state.challenges = null;
        state.validationsFiles = null;
        state.validationsBySession = null;
        state.userValidations = null;
        state.validatingChallenges = null;
    },
    SET_SESSIONS(state: any, sessions: any)
    {
      state.sessions = sessions;
    },
    SET_CHALLENGES(state: any, challenges: any)
    {
      state.challenges = challenges;
    },
    SET_VALIDATIONS_FILES(state: any, files: any)
    {
      state.validationsFiles = files;
    },
    SET_VALIDATING_CHALLENGES(state: any, challenges: any)
    {
      state.validatingChallenges = challenges;
    },
    SET_USER_VALIDATIONS(state: any, validations: any)
    {
      state.userValidations = validations;
    },
    SET_VALIDATIONS_BY_SESSION(state: any, validations: any)
    {
      state.validationsBySession = validations;
    }
  },
  actions: {
    saveUser({commit}, user: any){
      commit('SET_USER', user);
    },
    clearUser({ commit }) {
      commit('CLEAR_USER');
    },

    async getSessions({ commit, getters }) {
      const headers = {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + getters.apiToken
      }
      await axios.get('http://localhost:8000/api/sessions', { headers })
        .then((response: any) => {
          commit('SET_SESSIONS', response.data);
        }).catch((err:any) => {console.log(JSON.stringify(err.message))})
    },

    async getChallenges({ commit, getters }) {
      const headers = {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + getters.apiToken
      }
      await axios.get('http://localhost:8000/api/challenges', { headers })
          .then((response: any) => {
              console.log(response);
          commit('SET_CHALLENGES', response.data);
        }).catch((err:any) => {console.log(JSON.stringify(err.message))})
    },

    async refreshCurrentUser({ commit, getters }) {
      const current = getters.currentUser;

       const headers = {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + getters.apiToken
      }
      await axios.get('http://localhost:8000/api/user/'+current.id, { headers })
        .then((response: any) => {
          commit('SET_USER', response.data.res.data);
        }).catch((err:any) => {console.log(JSON.stringify(err.message))})
    },

    async getFilesByUser({commit, getters}, challenge_id: number)
    {
      const current = getters.currentUser;

       const headers = {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + getters.apiToken
      }
      await axios.get('http://localhost:8000/api/challenge/file/'+current.id, { headers })
        .then((response: any) => {
          commit('SET_VALIDATIONS_FILES', response.data)
        }).catch((err:any) => {console.log(JSON.stringify(err.message))})
    },

    async getValidatingChallenges({commit, getters, state})
    {
      state.loadingValidations = true;
      const current = getters.currentUser;

       const headers = {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + getters.apiToken
      }
      await axios.post('http://localhost:8000/api/validation/getValidatingChallenges',{user_id: current.id}, { headers })
        .then((response: any) => {
          commit('SET_VALIDATING_CHALLENGES', response.data.res.data);
          state.loadingValidations = false;
        }).catch((err:any) => {console.log(JSON.stringify(err.message))})
    },

    async getValidations({commit, getters, state})
    {
      state.loadingValidations = true;
      const current = getters.currentUser;

       const headers = {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + getters.apiToken
      }
      await axios.post('http://localhost:8000/api/validation/getByUser',{user_id: current.id}, { headers })
        .then((response: any) => {
          commit('SET_USER_VALIDATIONS', response.data.res.data)
          state.loadingValidations = false;
        }).catch((err:any) => {console.log(JSON.stringify(err.message))})
    },

    async getAllValidationsBySessionId({commit, getters, state}, session_id: number)
    {
      state.loadingValidations = true;
       const headers = {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + getters.apiToken
      }
      await axios.get('http://localhost:8000/api/session/'+session_id+'/validations', { headers })
        .then((response: any) => {
          commit('SET_VALIDATIONS_BY_SESSION', response.data.res.data)
          state.loadingValidations = false;
        }).catch((err: any) => { console.log(JSON.stringify(err.message)) })

    },



  },
  modules: {
  },
  getters: {
    currentUser: state => {
      return state.user;
    },
    apiToken: state => {
      return state.user.api_token;
    },
    allSessions: state => {
      return state.sessions;
    },
    userSession: state => {
      return state.user.session;
    },
    userChallenges: state => {
      return state.user.challenges;
    },
    userValidationsFiles: state => {
      return state.validationsFiles;
    },
    userValidatingChallenges: state => {
      return state.validatingChallenges;
    },
    userValidations: state => {
      return state.userValidations;
    },
    getSessionById: (state) => (id: number) => {
      return state.sessions.find((session: any) => session.id == id);
    },
    getValidationsBySession: (state) => {
      return state.validationsBySession;
    },
    validationsLoading: (state): boolean => {
      return state.loadingValidations;
    }
  }
})
