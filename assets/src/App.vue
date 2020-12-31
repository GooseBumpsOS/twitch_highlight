<script>
export default {
  name: 'App',
  data: () => ({
    el: 1,
  }),
  methods: {},
};
</script>

<template>
  <div id="app">
    <v-app id="inspire">
      <v-stepper v-model="e1">
        <v-stepper-header>
          <v-stepper-step
              :complete="e1 > 1"
              step="1"
          >
            Name of step 1
          </v-stepper-step>

          <v-divider></v-divider>

          <v-stepper-step
              :complete="e1 > 2"
              step="2"
          >
            Name of step 2
          </v-stepper-step>

          <v-divider></v-divider>

          <v-stepper-step step="3">
            Name of step 3
          </v-stepper-step>
        </v-stepper-header>

        <v-stepper-items>
          <v-stepper-content step="1">
            <v-card
                class="mb-12"
                color="grey lighten-1"
                height="200px"
            ></v-card>

            <v-btn
                color="primary"
                @click="e1 = 2"
            >
              Continue
            </v-btn>

            <v-btn text>
              Cancel
            </v-btn>
          </v-stepper-content>

          <v-stepper-content step="2">
            <v-card
                class="mb-12"
                color="grey lighten-1"
                height="200px"
            ></v-card>

            <v-btn
                color="primary"
                @click="e1 = 3"
            >
              Continue
            </v-btn>

            <v-btn text>
              Cancel
            </v-btn>
          </v-stepper-content>

          <v-stepper-content step="3">
            <v-card
                class="mb-12"
                color="grey lighten-1"
                height="200px"
            ></v-card>

            <v-btn
                color="primary"
                @click="e1 = 1"
            >
              Continue
            </v-btn>

            <v-btn text>
              Cancel
            </v-btn>
          </v-stepper-content>
        </v-stepper-items>
      </v-stepper>

      <notifications group="alert" position="center top"/>

    </v-app>
  </div>

</template>

<style lang="css">
</style>