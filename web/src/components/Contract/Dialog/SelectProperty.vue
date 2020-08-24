<template>

  <v-card outlined class="pa-4" >
    <v-card-title primary-title class="d-flex" >
      <span>Vincular Imóvel ao Contrato</span>
      <v-spacer></v-spacer>
    </v-card-title>
    <v-divider></v-divider>

    <v-row class="mx-3">
      <v-col cols="12">
        <v-text-field
          dense
          placeholder="Digite um texto para pesquisar"
          id="id"
          outlined
          hide-details
          append-icon="mdi-magnify"
          clearable
          v-model="searchTerm"
        ></v-text-field>
      </v-col>
    </v-row>
  
    <v-card-text style="max-height: 500px;">
      <v-row>
        
        <v-col cols="12">

          <v-data-table
            :headers="table.headers"
            :items="properties.data"
            :server-items-length="properties.total"
            class="elevation-0"
            :options.sync="table.options"
            :footer-props="{'items-per-page-options': [5,10,15,25,50,100]}"
            :loading="loading"
            show-select
            single-select
            v-model="table.selectedItems"
          >
 
            <template v-slot:item.address_street="{ item }">
              {{ utils.joinAddress(item) }}
            </template>

            <template v-slot:item.is_rent="{ item }">
              {{ item.is_rent ? 'Contratado' : 'Não Contratado' }}
            </template>

            <template v-slot:item.data-table-select="{ item, isSelected, select }">
              <v-simple-checkbox v-if="!item.is_rent" :value="isSelected" @input="select($event)"></v-simple-checkbox>
            </template>

          </v-data-table>
        </v-col>
      </v-row>
    </v-card-text>
    <v-divider></v-divider>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn text color="red" @click="$emit('close')">Cancelar</v-btn>
      <v-btn :disabled="table.selectedItems.length === 0" color="primary" @click="$emit('attach', table.selectedItems[0] )">Vincular</v-btn>
    </v-card-actions>
  </v-card>

</template>

<style scoped>

</style>

<script>

export default {

  components: {

  },

  inject: ['utils', 'propertyRepository'],

  mixins: [],

  props: [],

  watch: {
    
    'table.options' () {
      this.getPropertiesList()
      this.table.selectedItems = []
      this.table.expandedItems = []
    },

    searchTerm () {
      
      // ** clearInterval e setTimeout para evitar muitas consultas na api ** /
      clearInterval(this.throttleTimer)
      this.throttleTimer = setTimeout( () => {
        this.table.options.page = 1
        this.table.selectedItems = []
        this.table.expandedItems = []
        this.getPropertiesList()
      }, 500)

    }
  },

  data: () => ({ 

    table: {
      headers: [
        { text: 'Código', value: 'id', sortable: false, align: 'left', width:50 },
        { text: 'Endereço', value: 'address_street' },
        { text: 'Status', value: 'is_rent' },
      ],
      options: {
        page: 1,
        itemsPerPage: 5,
        sortBy: [],
        sortDesc: [],
        groupBy: [],
        groupDesc: [],
        multiSort: true,
        mustSort: false,
      },
      selectedItems: [],
      expandedItems: [],
    },

    loading: false,

    properties: {
      data: [],
      total: 0
    },

    throttleTimer: null,
    searchTerm: null,
    
  }),

  computed: {
    
  },

  created () {
    
  },

  mounted() {
    
  },

  methods: {

    getPropertiesList() {
      
      var param = {
        page: this.table.options.page,
        sortBy: this.table.options.sortBy,
        sortDesc: this.table.options.sortDesc,
        perPage: this.table.options.itemsPerPage,
        searchTerm: this.searchTerm,
      }

      var queryString = Object.keys(param).map(key => key + '=' + (param[key] === null ? '' : param[key])).join('&');

      this.loading = true
      this.propertyRepository.get(queryString)
        .then( response => {
          this.properties.data = response.data.data
          this.properties.total = response.data.total
          this.loading = false
        })
        .catch( () => this.loading = false )
    },

  },
}

</script>