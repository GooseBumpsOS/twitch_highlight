<script>
import Stepper from './Components/Stepper';
import Api from './Service/Api';
import {VueFrappe} from 'vue2-frappe';

export default {
  name: 'App',
  components: {Stepper, VueFrappe},
  data() {
    return {
      isShowStepper: true,
      isAnalysisReady: false,
    };
  },
  methods: {
    onComplete(link, volume, keywords) {
      let api = new Api();
      let videoId = link.match(/(?!videos\/)\d+(?!\/)/mg)[0];
      api.post('/api/work/', {
        videoId,
        'coeff': volume,
        keywords,
        'user': 'user',
      }).then(() => {
        this.isShowStepper = false;
        this.$notify({
          group: 'alert',
          title: 'Все ок',
          type: 'success',
          text: 'Мы уже начали собирать данные по этому видео, обычно это занимает не больше 10 минут',
          duration: 8000,
        });
        setInterval(this.isReady(videoId), 5000);
      });
    },
    isReady(videoId) {
      let api = new Api();
      api.get(`/api/work/user/${videoId}`).then(() => {
            alert('ready');
          },
      );
    },
  },
};
</script>

<template>
  <div id="app">
    <v-app id="inspire">

      <stepper
          v-if="isShowStepper"
          @complete-stepper="onComplete"
      />


      <notifications group="alert" position="center top"/>

    </v-app>
  </div>

</template>

<style lang="css">
</style>