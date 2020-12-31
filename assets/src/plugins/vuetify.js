import '@mdi/font/css/materialdesignicons.css';
import Vue from 'vue';
import Vuetify from 'vuetify/lib';

Vue.use(Vuetify);

export default new Vuetify({
  theme: {
    dark: false,
    themes: {
      light: {
        background: '#292930',
        primary: {
          base: '#FF9900',
          darken3: '#9E5F00'
        },
        secondary: {
          base: '#1F1A32',
          lighten2: "#504280",
          lighten3: '#5A4F80',
          darken3: '#ff6200'
        },
      }
    }
  },
  icons: {
    iconfont: 'mdi',
  },
});
