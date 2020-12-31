const axios = require('axios');
import Vue from 'vue';

class Api {

  async get(path, data) {
    return axios({
      method: 'get',
      url: path,
      responseType: 'json',
      params: data,
    }).catch((error) => {
          Vue.notify({
            group: 'alert',
            title: 'Error',
            type: 'error',
            text: error.response.data.message,
          });
        },
    );
  }

  async post(path, data) {
    return axios({
      method: 'post',
      url: path,
      responseType: 'json',
      data: data,
    }).catch((error) => {
          Vue.notify({
            group: 'alert',
            title: 'Error',
            type: 'error',
            text: error.response.data.message,
          });

          throw new Error('Something wrong with request');
        },
    );
  }

}

export default Api;