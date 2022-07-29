import Vue from 'vue'
import router from '~/router'
import App from '~/App'
import VModal from 'vue-js-modal'


import '~/components'

Vue.config.productionTip = false
Vue.use(VModal);

/* eslint-disable no-new */
new Vue({
  router,
  ...App
})
