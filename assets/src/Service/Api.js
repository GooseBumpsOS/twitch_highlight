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
            text: this._getErrorMsg(error),
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
            text: this._getErrorMsg(error),
          });

          throw new Error('Something wrong with request');
        },
    );
  }

  _getErrorMsg(error) {
    let errorMsg = 'Упсс. Похоже что-то не так.';
    if (error.response.data !== null) {
      errorMsg = error.response.data.message;
    }

    return errorMsg;
  }

}

export default Api;