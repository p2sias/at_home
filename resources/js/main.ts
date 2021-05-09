import Vue from 'vue';
import VueRouter from 'vue-router';

import router from './Router/index';
import store from './Store/index';
import App from './App.vue';
import vuetify from './Vuetify/index';
import VueSimpleMarkdown from 'vue-simple-markdown';
import 'vue-simple-markdown/dist/vue-simple-markdown.css'

Vue.use(VueRouter);
Vue.use(VueSimpleMarkdown);

new Vue({
  router,
  store,
  vuetify,
  render: h => h(App)
}).$mount('#app')
