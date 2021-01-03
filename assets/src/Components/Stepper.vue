<script>
import Api from '../Service/Api';

export default {
  name: 'Stepper',
  data() {
    return {
      slider: {label: 'Коэффициент', val: 1.2, color: 'red'},
      loading: false,
      keywords: 'LUL, ха, ах, ъх, бан, kappa, biblethump, kek, lol, лол',
      currentStep: 1,
      link: '',
      linkRule: [
        v => !!v || 'Ссылка обязательна',
        v => /(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/.test(
            v) || 'Ссылка не валидна',
      ],
    };
  },
  methods: {
    validateVideo() {
      let api = new Api();
      let videoId = this.link.match(/(?!videos\/)\d+(?!\/)/mg)[0];
      this.loading = true

      api.get(`/api/validate/video_exist/${videoId}`).then((resp) => {
        if (resp.data === false){
          this.$notify({
            group: 'alert',
            title: 'Ошибка ссылки',
            type: 'warn',
            text: 'Похоже, что мы не можем получить это видео.',
          });
        } else {
          this.currentStep = 2;
        }
      }).finally(() => {
        this.loading = false;
      })
    },
  },
};
</script>

<template>
  <v-stepper v-model="currentStep">
    <v-stepper-header>
      <v-stepper-step
          :complete="currentStep > 1"
          step="1"
      >
        Какое видео?
      </v-stepper-step>

      <v-divider></v-divider>

      <v-stepper-step
          :complete="currentStep > 2"
          step="2"
      >
        Какой коэффициент роста?
      </v-stepper-step>

      <v-divider></v-divider>

      <v-stepper-step step="3">
        Ключевые слова
      </v-stepper-step>
    </v-stepper-header>

    <v-stepper-items>
      <v-stepper-content step="1">

        <v-text-field
            v-model="link"
            :rules="linkRule"
            label="Ссылка на видео"
            required
        ></v-text-field>

        <v-btn
            class="mt-2"
            color="primary"
            :loading="loading"
            @click="validateVideo"
        >
          Далее
        </v-btn>

      </v-stepper-content>

      <v-stepper-content step="2">
        <v-slider
            class="mt-12"
            v-model="slider.val"
            step="0.1"
            :min="0.1"
            :max="10"
            :label="slider.label"
            :thumb-color="slider.color"
            thumb-label="always"
        ></v-slider>

        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-icon
                color="primary"
                dark
                v-bind="attrs"
                v-on="on"
            >
              mdi-comment-question
            </v-icon>
          </template>
          <span>Коэффициент роста показывает, как сильно должно измениться кол-во ключевых слов за одну минуту</span>
        </v-tooltip>

        <v-btn
            class="mt-2"
            color="primary"
            @click="currentStep = 3"
        >
          Далее
        </v-btn>

        <v-btn text
               @click="currentStep = 1"
        >
          Назад
        </v-btn>
      </v-stepper-content>

      <v-stepper-content step="3">
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-icon
                color="primary"
                dark
                v-bind="attrs"
                v-on="on"
            >
              mdi-comment-question
            </v-icon>
          </template>
          <span>На какие ключевые слова стоит роботу обратить внимание, вводится через запятую</span>
        </v-tooltip>
        <v-text-field v-model="keywords" label="Ключевые слова через запятую"></v-text-field>

        <v-btn
            color="primary"
            :loading="loading"
            @click="$emit('complete-stepper', link, slider.val, keywords); loading = true"
        >
          Покажи мне анализ
        </v-btn>

        <v-btn text
               @click="currentStep = 2"
        >
          Назад
        </v-btn>
      </v-stepper-content>
    </v-stepper-items>
  </v-stepper>
</template>

<style scoped>

</style>