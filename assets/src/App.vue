<script>
import Stepper from './Components/Stepper';
import Api from './Service/Api';
import Graph from './Components/Graph';
import CsvExporter from './Service/CsvExporter';
import TimeConverter from './Service/TimeConverter';
import TwitchHelper from './Service/TwitchHelper';

export default {
  name: 'App',
  components: {Graph, Stepper},
  data() {
    return {
      isShowStepper: true,
      isAnalysisReady: false,
      messages: {
        isLoading: false,
        data: [],
      },
      intervalId: -1,
      analysisData: {
        videoId: null,
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
    convertToCsv() {
      let exportCsv = () => {
        let exportData = [];

        this.messages.data.msg.forEach((currentEl, index) => {
          exportData.push([currentEl, this.messages.data.offset[index]]);
        });

        CsvExporter.exportCsv(exportData);
      };

      if (this.messages.data.msg === undefined) {
        this.getChatMessages(exportCsv);
      } else {
        exportCsv();
      }
    },
    createMessage(msg, emoticons) {
      return TwitchHelper.convertSerMessage(msg, emoticons);
    },
    getChatMessages(callback = null) {
      let api = new Api();
      this.messages.isLoading = true;
      api.get(`/api/chat/${this.analysisData.videoId}`).then((resp) => {
        this.messages.data = resp.data;

        if (callback !== null) {
          callback(resp);
        }
      }).finally(() => {
        this.messages.isLoading = false;
      });
    },
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
      return TwitchHelper.makeTwitchUrl(this.analysisData.videoId, offset);
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
      return TimeConverter.convert(offset);
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
                :labels="analysisData.labels.map((val) => {return val * 60}).map(makeTimeFromOffset)"
                title="Активность по ключевым словам(хайлайты)"
            />
          </v-col>
        </v-row>

        <v-row>
          <v-col>
            <graph
                id-graph="chatActivity"
                :data-set="analysisData.chatActivity"
                :labels="analysisData.labels.map((val) => {return val * 60}).map(makeTimeFromOffset)"
                title="Активность чата"
            />
          </v-col>
        </v-row>

        <v-row>
          <v-col>
            <v-responsive
                max-width="600"
                class="mx-auto mb-4"
            >
              <v-row>
                <v-col>
                  <v-btn
                      v-if="messages.data.length === 0"
                      @click="getChatMessages"
                      :loading="messages.isLoading"
                  >
                    Получить сообщения чата
                  </v-btn>
                </v-col>

                <v-col>
                  <v-btn
                      @click="convertToCsv"
                  >
                    Скачать чат(CSV)
                  </v-btn>
                </v-col>
              </v-row>
            </v-responsive>

            <v-card
                v-show="messages.data.length !== 0"
                elevation="16"
                max-width="600"
                class="mx-auto"
            >
              <v-virtual-scroll
                  :items="messages.data.msg"
                  height="550"
                  item-height="64"
              >
                <template v-slot:default="{ item, index }">
                  <v-list-item :key="item">
                    <v-list-item-action>
                      <v-btn
                          small
                          rounded
                          depressed
                          color="primary"
                      >
                        <small>{{ makeTimeFromOffset(messages.data.offset[index]) }}</small>
                      </v-btn>
                    </v-list-item-action>

                    <v-list-item-content>
                      <v-list-item-title v-html="createMessage(item, messages.data.emoticons[index])">
                      </v-list-item-title>
                    </v-list-item-content>

                  </v-list-item>

                  <v-divider></v-divider>
                </template>
              </v-virtual-scroll>
            </v-card>
          </v-col>

        </v-row>

        <v-row class="mt-12">
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