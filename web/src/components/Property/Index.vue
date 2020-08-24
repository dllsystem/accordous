<template>

  <v-card outlined class="ma-4" >
    <v-card-title primary-title>
      <span>Listagem de Imóveis</span>
      <v-spacer></v-spacer>
      <v-btn icon @click="tableOptionsDialog.show=!tableOptionsDialog.show">
        <v-icon>mdi-cog</v-icon>
      </v-btn>
    </v-card-title>
    <v-divider></v-divider>
    <v-card-text>
      <v-row>
        <v-col cols="12" sm="6" class="d-flex align-center my-0 py-0">
          <v-btn color="primary" @click="createProperty">
            <v-icon class="mr-2">mdi-plus</v-icon>
            Novo imóvel
          </v-btn>
          <v-btn 
            v-if="table.selectedItems.length == 1" 
            color="success" class="ml-2" 
            @click="editItem(table.selectedItems[0])"
          >
            <v-icon class="mr-2">mdi-pencil</v-icon>
            Editar
          </v-btn>
          <v-btn 
            v-if="table.selectedItems.length > 0" 
            color="error" class="ml-2"
            @click="deleteMultipleItems"  
          >
            <v-icon class="mr-2">mdi-trash-can</v-icon>
            Deletar
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
            :items="properties.data"
            :server-items-length="properties.total"
            class="elevation-0"
            :options.sync="table.options"
            :footer-props="{'items-per-page-options': [5,10,15,25,50,100]}"
            :loading="loading"
            :show-select="tableOptionsDialog.showSelect"
            v-model="table.selectedItems"
            :show-expand="tableOptionsDialog.showExpand"
            :expanded.sync="table.expandedItems"
          >
            <template v-slot:item.action="{ item }">
                <v-btn icon :to="{name: 'property.edit', params: {id:item.id}}">
                  <v-icon color="green">mdi-pencil</v-icon>
                </v-btn>
                <v-btn icon @click="deleteItem(item)">
                  <v-icon color="red">mdi-trash-can</v-icon>
                </v-btn>
            </template>
 
            <template v-slot:item.address_street="{ item }">
              {{ utils.joinAddress(item) }}
            </template>

            <template v-slot:item.is_rent="{ item }">
              {{ item.is_rent ? 'Contratado' : 'Não Contratado' }}
            </template>

            <template v-slot:expanded-item="{ item }">
              <td></td>
              <td></td>
              <td>{{item.owner_name}}</td>
              <td>CEP {{item.address_zipcode}}</td>
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
          v-if="deleteDialog.propertyItem"
          max-width="600"
        >
          <v-card outlined class="pa-4" >
            <v-card-title primary-title class="d-flex" >
              <span>Deletar Imóvel</span>
              <v-spacer></v-spacer>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text class="mt-6" >
              <v-row>
                <v-col cols="12" sm="3" class="d-flex justify-center align-center">
                  <v-icon v-if="deleteDialog.propertyItem.is_rent" color="red" size="100" >mdi-close-circle-outline</v-icon>
                  <v-icon v-else color="primary" size="100" >mdi-help-circle-outline</v-icon>
                </v-col>
                <v-col class="d-flex align-center">
                  <v-row dense no-gutters class="text-center text-sm-left">
                    
                    <v-col cols="12">
                      <span v-if="deleteDialog.propertyItem.is_rent" class="text-subtitle-1 font-weight-bold">Não é possível deletar o imóvel!</span>
                      <span v-else class="text-subtitle-1 font-weight-bold">Tem certeza que deseja deletar o imóvel?</span>
                    </v-col>
                    <v-col cols="12">
                      <span v-text="utils.joinAddress(deleteDialog.propertyItem)"></span>
                    </v-col>
                    
                    <v-row v-if="deleteDialog.propertyItem.contract" dense no-gutters class="d-flex flex-column mt-2">
                      <v-col cols="12">
                        <span class="text-subtitle-1 font-weight-bold">Ele esta associado ao contrato:</span>
                      </v-col>
                      <v-col >
                        <span>No. {{deleteDialog.propertyItem.contract.id}}</span>
                      </v-col>
                      <v-col v-if="deleteDialog.propertyItem.contract">
                        <span>{{deleteDialog.propertyItem.contract.tenant_name}}</span>
                      </v-col>
                      <v-col v-if="deleteDialog.propertyItem.contract">
                        <span>{{deleteDialog.propertyItem.contract.tenant_email}}</span>
                      </v-col>
                    </v-row>
                  </v-row>
                </v-col>
              </v-row>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn text color="red" @click="deleteDialog.show=!deleteDialog.show">Cancelar</v-btn>
              <v-btn 
                v-if="!deleteDialog.propertyItem.is_rent"   
                color="primary" 
                @click="deleteProperty(deleteDialog.propertyItem)" 
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
import Swal from 'sweetalert2';

export default {

  components: {
    
  },

  inject: ['utils', 'propertyRepository'],

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

    throttleTimer: null,
    searchTerm: null,
    
    loading: false,

    properties: {
      data: [],
      total: 0
    },

    table: {
      headers: [
        { text: 'Email', value: 'owner_email' },
        { text: 'Endereço', value: 'address_street' },
        { text: 'Status', value: 'is_rent' },
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
      propertyItem:null,
    },

    tableOptionsDialog : {
      show: false,
      dense: false,
      showSelect: true,
      showExpand: true
    }
    
  }),

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

    editItem(item) {
      this.$router.push({name:'property.edit', params: {id:item.id}})
    },

    deleteItem(item) {
      this.deleteDialog.propertyItem = item
      this.deleteDialog.show = true
    },

    deleteMultipleItems() {
      if (this.table.selectedItems.length === 1) {
        this.deleteItem(this.table.selectedItems[0])
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          html: 'Ainda estamos trabalhando nisso!<br><br>Por enquanto, delete um item por vez.',
          
        })
      }
    },

    createProperty() {
      this.$router.push({name: 'property.create'})
    },

    deleteProperty(item) {
      
      if (!item) return

      this.loading = true
      this.propertyRepository.delete(item.id)
        .then( () => {
          let deleteIdx =  this.properties.data.findIndex(el => el.id === item.id)
          this.properties.data.splice(deleteIdx, 1)
          this.deleteDialog.show = false
          this.properties.total--
          this.table.selectedItems = []
          this.table.expandedItems = []
          this.deleteDialog.propertyItem = null
          this.loading = false
        })
        .catch( () => this.loading = false )
    },
    
  },

  created () {
    this.getPropertiesList()
  }

};
</script>
