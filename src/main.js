import Vue from 'vue'
import App from './App.vue'
import router from './router'
import Vuetify from 'vuetify'

Vue.config.productionTip = false;

new Vue({
  router,
  vuetify: new Vuetify({
    theme: {
      dark: true,
    },
  }),
  render: h => h(App)
}).$mount('#app');
