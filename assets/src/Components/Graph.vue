<script>
import {VueFrappe} from 'vue2-frappe';

export default {
  name: 'Graph',
  components: {VueFrappe},
  props: {
    idGraph: String,
    title: String,
    labels: Array,
    dataSet: Object,
    colors: {
      type: Array,
      default: () => ['red'],
    },
  },
};
</script>

<template>
  <vue-frappe
      :id="idGraph"
      :title="title"
      type="line"
      :height="300"
      :lineOptions="{regionFill: 1}"
      :colors="['red']"
      :labels="labels"
      :discreteDomains="false"
      :dataSets="[dataSet]"
  ></vue-frappe>
</template>

<style scoped>

</style>