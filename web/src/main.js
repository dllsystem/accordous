import Vue from 'vue'
import './plugins/axios'
import App from './App.vue'
import './registerServiceWorker'
import router from './router'
import store from './store'
import vuetify from './plugins/vuetify'
import './plugins/vee-validate'
import './plugins/vue-the-mask'
import ServiceContainer from './services/ServiceContainer';
import utils from './utils/utils'

Vue.config.productionTip = false

Vue.directive('disabledIconFocus', {
  bind(el) {
    el.querySelectorAll('.v-input__icon button').forEach(item => item.setAttribute('tabindex', -1));
  },
})

new Vue({
  provide: {
    ...ServiceContainer, utils
  },
  router,
  store,
  vuetify,
  render: h => h(App)
}).$mount('#app')
