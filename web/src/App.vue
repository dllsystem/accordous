<template>
  <v-app>
    <v-navigation-drawer
     v-model="drawer"
      app
      clipped
      class="elevation-2"
      width="180"
      >
      <v-list dense>
        
        <v-list-item link :to="{name:'property'}">
          <v-list-item-action>
            <v-icon color="grey darken-1">mdi-home-city</v-icon>
          </v-list-item-action>
          <v-list-item-title >Imóveis</v-list-item-title>
        </v-list-item>

        <v-list-item class="mt-2" link :to="{name:'contract'}">
          <v-list-item-action>
            <v-icon color="grey darken-1">mdi-file-document-multiple</v-icon>
          </v-list-item-action>
          <v-list-item-title >Contratos</v-list-item-title>
        </v-list-item>
        
      </v-list>
    </v-navigation-drawer>

    <v-app-bar
      app
      clipped-left
      dark
      dense
      >
      <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
      <div class="d-flex align-center" @click="goHome" style="cursor:pointer">
        <v-img
          alt="Vuetify Logo"
          class="shrink mr-2"
          contain
          src="https://accordous.com.br/wp-content/uploads/2020/05/branco-png.png"
          transition="scale-transition"
          width="200"
        />
      </div>

      <v-spacer></v-spacer>
      
    </v-app-bar>

    <v-main>
      <router-view></router-view>
    </v-main>

  </v-app>
</template>

<style>
  .w-100 {
    width: 100%;
  }

</style>

<script>

export default {
  name: 'App',

  components: {
    
  },

  data: () => ({
    drawer: true
  }),

  created () {
    
  },

  methods: {
    goHome() {
      this.$router.push({name:'home'})
    }
  }
};
</script>
