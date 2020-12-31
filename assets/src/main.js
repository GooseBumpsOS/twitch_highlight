import Vue from 'vue'
import vuetify from './plugins/vuetify'
import './plugins/notifications'
import App from './App'

Vue.config.productionTip = false

new Vue({
  vuetify,
  render: h => h(App)
}).$mount('#app')