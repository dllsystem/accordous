<template>

  <v-card outlined class="ma-4" >
    <v-card-title primary-title>
      <span>Listagem de Contratos</span>
      <v-spacer></v-spacer>
      <v-btn icon @click="tableOptionsDialog.show=!tableOptionsDialog.show">
        <v-icon>mdi-cog</v-icon>
      </v-btn>
    </v-card-title>
    <v-divider></v-divider>
    <v-card-text>
      <v-row>
        <v-col cols="12" sm="6" class="d-flex align-center my-0 py-0">
          <v-btn color="primary" @click="createContract">
            <v-icon class="mr-2">mdi-plus</v-icon>
            Novo contrato
          </v-btn>
          <v-spacer></v-spacer>
        </v-col>
        <v-col cols="12" sm="6">
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

        <v-col cols="12" class="mt-1">
          <v-divider></v-divider>
        </v-col>
        
        <v-col cols="12" class="mt-0 pt-0">

          <v-data-table
            :dense="tableOptionsDialog.dense"
            :headers="table.headers"
            :items="contracts.data"
            :server-items-length="contracts.total"
            class="elevation-0"
            :options.sync="table.options"
            :footer-props="{'items-per-page-options': [5,10,15,25,50,100]}"
            :loading="loading"
            :show-select="tableOptionsDialog.showSelect"
            v-model="table.selectedItems"
            :show-expand="tableOptionsDialog.showExpand"
            :expanded.sync="table.expandedItems"
          >
            <template slot="headers" slot-scope="props">
              <tr v-if="$vuetify.breakpoint.xs" style="display:none">
                </tr>
                <tr v-else>
                  <th v-for="header in props.headers" :key="header.text" >
                    {{ header.text }}
                  </th>
                </tr>
            </template>
            <template v-slot:item.action="{ item }">
                <v-btn icon :to="{name: 'contract.edit', params: {id:item.id}}">
                  <v-icon color="green">mdi-pencil</v-icon>
                </v-btn>
                <v-btn icon @click="deleteItem(item)">
                  <v-icon color="red">mdi-trash-can</v-icon>
                </v-btn>
            </template>
            <template v-slot:item.property="{ item }">
              {{ utils.joinAddress(item.property) }}
            </template>
          </v-data-table>
        </v-col>

        <v-dialog
          v-model="tableOptionsDialog.show"
          max-width="600"
        >
          <v-card outlined class="pa-4" >
            <v-card-title primary-title class="d-flex" >
              <span>Opções de visualização da tabela</span>
              <v-spacer></v-spacer>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text >
              <div class="d-flex flex-column">
                <v-checkbox label="Exibição compacta" hide-details v-model="tableOptionsDialog.dense"></v-checkbox>
                <v-checkbox label="Exibir expandir" hide-details v-model="tableOptionsDialog.showExpand"></v-checkbox>
                <v-checkbox label="Exibir selecionar" hide-details v-model="tableOptionsDialog.showSelect"></v-checkbox>
              </div>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="primary" @click="tableOptionsDialog.show=false">Fechar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog
          v-model="deleteDialog.show"
          v-if="deleteDialog.contractItem"
          max-width="600"
        >
          <v-card outlined class="pa-4" >
            <v-card-title primary-title class="d-flex" >
              <span>Deletar Contrato</span>
              <v-spacer></v-spacer>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text class="mt-6" >
              <v-row>
                <v-col cols="12" sm="3" class="d-flex justify-center align-center">
                  <v-icon color="primary" size="100" >mdi-help-circle-outline</v-icon>
                </v-col>
                <v-col class="d-flex align-center">
                  <v-row dense no-gutters class="text-center text-sm-left">
                    
                    <v-col cols="12">
                      <span class="text-subtitle-1 font-weight-bold">Tem certeza que deseja deletar o Contrato?</span>
                    </v-col>
                    <v-col cols="12">
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn text color="red" @click="deleteDialog.show=!deleteDialog.show">Cancelar</v-btn>
              <v-btn 
                color="primary" 
                @click="deleteContract(deleteDialog.contractItem)" 
                :loading="loading"
              >Confirmar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
          
      </v-row>
    </v-card-text>
  </v-card>

</template>

<script>


export default {

  components: {
    
  },

  inject: ['utils', 'contractRepository'],

  watch: {
    
    'table.options' () {
      this.getContractsList()
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
        this.getContractsList()
      }, 500)

    }
  },

  data: () => ({ 

    throttleTimer: null,
    searchTerm: null,
    
    loading: false,

    contracts: {
      data: [],
      total: 0
    },

    table: {
      headers: [
        // { text: 'Nome', value: 'tenant_name' },
        { text: 'Email', value: 'tenant_email' },
        // { text: 'Documento', value: 'tenant_document', sortable: false },
        { text: 'Imóvel', value: 'property' },
        { text: 'Ações', value: 'action', sortable: false, align: 'right', width: '110' },
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

    deleteDialog: {
      show: false,
      contractItem:null,
    },

    tableOptionsDialog : {
      show: false,
      dense: false,
      showSelect: false,
      showExpand: false
    }
    
  }),

  methods: {

    getContractsList() {
      
      var param = {
        page: this.table.options.page,
        sortBy: this.table.options.sortBy,
        sortDesc: this.table.options.sortDesc,
        perPage: this.table.options.itemsPerPage,
        searchTerm: this.searchTerm,
      }

      var queryString = Object.keys(param).map(key => key + '=' + (param[key] === null ? '' : param[key])).join('&');

      this.loading = true

      this.contractRepository.get(queryString)
        .then( response => {
          this.contracts.data = response.data.data
          this.contracts.total = response.data.total
          this.loading = false
        }) 
        .catch( () => this.loading = false )
    },

    deleteItem(item) {
      this.deleteDialog.contractItem = item
      this.deleteDialog.show = true
    },

    createContract() {
      this.$router.push({name: 'contract.create'})
    },

    deleteContract(item) {

      if (!item) return

      this.loading = true
      this.contractRepository.delete(item.id)
        .then( () => {
          let deleteIdx =  this.contracts.data.findIndex(el => el.id === item.id)
          this.contracts.data.splice(deleteIdx, 1)
          this.deleteDialog.show = false
          this.contracts.total--
          this.table.selectedItems = []
          this.table.expandedItems = []
          this.deleteDialog.contractItem = null
          this.loading = false
        })
        .catch( () => this.loading = false )

    },
    
  },

  created () {
    this.getContractsList()
  }

};
</script>
