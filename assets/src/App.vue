<script>
import Stepper from './Components/Stepper';
import Api from './Service/Api';

export default {
  name: 'App',
  components: {Stepper},
  data() {
    return {};
  },
  methods: {
    onComplete(link, volume, keywords) {
      let api = new Api();
      let videoId = link.match(/(?!videos\/)\d+(?!\/)/mg)[0];
      api.post('/api/new-work/', {
        videoId,
        volume,
        keywords,
        "user": "user"
      })
    },
  },
};
</script>

<template>
  <div id="app">
    <v-app id="inspire">

      <stepper
          @complete-stepper="onComplete"
      />

      <notifications group="alert" position="center top"/>

    </v-app>
  </div>

</template>

<style lang="css">
</style>