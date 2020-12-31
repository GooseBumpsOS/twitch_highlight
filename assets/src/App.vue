<script>
import Stepper from './Components/Stepper';

export default {
  name: 'App',
  components: {Stepper},
  data() {
    return {};
  },
  methods: {
    onComplete(link, volume, keywords) {

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