<script>
import Stepper from './Components/Stepper';
import Api from './Service/Api';
import Graph from './Components/Graph';

export default {
  name: 'App',
  components: {Graph, Stepper},
  data() {
    return {
      isShowStepper: true,
      isAnalysisReady: false,
      analysisData: {
        chatActivity: {
          values: null,
        },
        chatAnalyseByCriteria: {
          values: null,
        },
        highLiteOffset: {},
      },
      data: [
        {
          values: [18, 40, 30, 35, 8, 52, 17, -4],
        },
      ],
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
        setInterval(this.isReadyInterval(videoId), 5000);
      });
    },
    isReadyInterval(videoId) {
      let api = new Api();
      api.get(`/api/work/user/${videoId}`).then((resp) => {
            this.isAnalysisReady = true;
            this.analysisData = resp.data;
            this.analysisData.labels = [...Array(resp.data.chatActivity.values.length).keys()];

          },
      );
    },
    makeTimeFromOffset(offset) {
      let date = new Date(offset * 1000);

      let minutes = date.getMinutes();
      if (minutes.length === 1){
        minutes = '0' + minutes;
      }

      let seconds = date.getSeconds();
      if (seconds.length === 1){
        seconds = '0' + seconds;
      }

      let hours = date.getHours() - 3;
      if (hours.length === 1){
        hours = '0' + hours;
      }

      return hours + ':' + minutes+ ':' + seconds;
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

      <v-container v-if="isAnalysisReady">
        <v-row>
          <v-col>
            <graph
                id-graph="highlight"
                :data-set="analysisData.chatAnalyseByCriteria"
                :labels="analysisData.labels.map(makeTimeFromOffset)"
                title="Хайлайты"
            />
          </v-col>
        </v-row>

        <v-row>
          <v-col>
            <graph
                id-graph="chatActivity"
                :data-set="analysisData.chatActivity"
                :labels="analysisData.labels"
                title="Активность чата"
            />
          </v-col>
        </v-row>
      </v-container>

      <notifications group="alert" position="center top"/>

    </v-app>
  </div>

</template>

<style lang="css">
</style>