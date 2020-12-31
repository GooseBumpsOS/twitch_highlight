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
      intervalId: -1,
      analysisData: {
        chatActivity: {
          values: null,
        },
        chatAnalyseByCriteria: {
          values: null,
        },
        highLiteOffset: {},
        highLiteTime: [],
      },
    };
  },
  watch: {
    isAnalysisReady: function(newStatus, oldStatus) {
      clearInterval(this.intervalId);
    },
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
        this.intervalId = setInterval(this.isReadyInterval, 5000, videoId);
      });
    },
    getTwitchUrl(offset) {
      return `https://www.twitch.tv/videos/688433658?t=${offset}s`;
    },
    isReadyInterval(videoId) {
      let api = new Api();
      api.get(`/api/work/user/${videoId}`).then((resp) => {
            if (resp.status === 200) {
              this.isAnalysisReady = true;
              this.analysisData = resp.data;
              this.analysisData.labels = [...Array(resp.data.chatActivity.values.length).keys()];
              this.analysisData.highLiteTime = this.analysisData.highLiteOffset.map(this.makeTimeFromOffset);
            }
          },
      );
    },
    makeTimeFromOffset(offset) {
      let date = new Date(offset * 1000);

      let minutes = date.getMinutes();
      if (minutes.length === 1) {
        minutes = '0' + minutes;
      }

      let seconds = date.getSeconds();
      if (seconds.length === 1) {
        seconds = '0' + seconds;
      }

      let hours = date.getHours() - 3;
      if (hours.length === 1) {
        hours = '0' + hours;
      }

      return hours + ':' + minutes + ':' + seconds;
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

      <div class="text-center mt-12">
        <v-progress-circular
            v-if="!isShowStepper && !isAnalysisReady"
            :width="3"
            color="green"
            indeterminate
        ></v-progress-circular>
      </div>

      <v-container v-if="isAnalysisReady">

        <div class="text-center mt-4">
          <v-btn
              @click="isAnalysisReady = false; isShowStepper = true"
              color="primary"
              elevation="2"
              large
          >На главную
          </v-btn>
        </div>


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
                :labels="analysisData.labels.map(makeTimeFromOffset)"
                title="Активность чата"
            />
          </v-col>
        </v-row>

        <v-row>
          <v-card
              class="mx-auto"
              max-width="800"
              tile
          >

            <v-list-item two-line v-for="(highlightOffset, index) in analysisData.highLiteOffset" :key="index">
              <v-list-item-content>
                <v-list-item-title>Хайлайт на: {{ analysisData.highLiteTime[index] }}</v-list-item-title>
                <v-list-item-subtitle><a target="_blank" :href="getTwitchUrl(highlightOffset)">Посмотреть на Twitch</a>
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>

          </v-card>
        </v-row>
      </v-container>

      <notifications group="alert" position="center top"/>

    </v-app>
  </div>

</template>

<style lang="css">
</style>