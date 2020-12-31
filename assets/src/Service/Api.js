const axios = require('axios');
import Vue from 'vue';

class Api {

  async get(path, data, token = '') {
    return axios({
      method: 'get',
      url: path,
      responseType: 'json',
      headers: this._getHeaders(token),
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

  async post(path, data, token = '') {
    return axios({
      method: 'post',
      url: path,
      responseType: 'json',
      headers: this._getHeaders(token),
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

  _getHeaders(token) {
    let result = {};
    if (token !== '') {
      result = {'Authorization': `BEARER ${token}`};
    }

    return result;
  }
}

export default Api;