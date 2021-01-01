<script>
export default {
  name: 'Stepper',
  data() {
    return {
      slider: {label: 'Коэффициент', val: 1.2, color: 'red'},
      loading: false,
      keywords: 'LUL, ха, ах, ъх, бан, kappa, biblethump, kek, lol, лол',
      e1: 1,
      link: '',
      linkRule: [
        v => !!v || 'Ссылка обязательна',
        v => /(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/.test(
            v) || 'Ссылка не валидна',
      ],
    };
  },
};
</script>

<template>
  <v-stepper v-model="e1">
    <v-stepper-header>
      <v-stepper-step
          :complete="e1 > 1"
          step="1"
      >
        Какое видео?
      </v-stepper-step>

      <v-divider></v-divider>

      <v-stepper-step
          :complete="e1 > 2"
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
            @click="e1 = 2"
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

        <v-btn
            class="mt-2"
            color="primary"
            @click="e1 = 3"
        >
          Далее
        </v-btn>

        <v-btn text
               @click="e1 = 1"
        >
          Назад
        </v-btn>
      </v-stepper-content>

      <v-stepper-content step="3">
        <v-text-field v-model="keywords" label="Ключевые слова через запятую"></v-text-field>

        <v-btn
            color="primary"
            :loading="loading"
            @click="$emit('complete-stepper', link, slider.val, keywords); loading = true"
        >
          Покажи мне анализ
        </v-btn>

        <v-btn text
               @click="e1 = 2"
        >
          Назад
        </v-btn>
      </v-stepper-content>
    </v-stepper-items>
  </v-stepper>
</template>

<style scoped>

</style>