<template>
  
  <v-card outlined class="ma-4" >
    <v-card-title primary-title>
      <span v-if="isEditMode">Editar Contrato</span>
      <span v-else>Cadastrar Contrato</span>
      <v-spacer></v-spacer>
    </v-card-title>
    <v-divider></v-divider>
    
    <ValidationObserver ref="observer">
      <v-form @submit.prevent="submit">
        <v-card-text>
          <v-row>
            <v-col cols="12">
              <v-radio-group 
                @change="contract.tenant_document=null" 
                v-model="contract.tenant_document_type" 
                class="ma-0 pa-0" 
                hide-details
                row 
              >
                <v-radio label="Pessoa Física" value="cpf"></v-radio>
                <v-radio label="Pessoa Jurídica" value="cnpj"></v-radio>
              </v-radio-group>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" md="4">
              <ValidationProvider v-slot="{ errors, failed }" name="NOME" rules="required|min:3|max:255">
                <v-text-field
                  label="Nome do Contratante"
                  outlined
                  v-model="contract.tenant_name"
                  :error-messages="errors"
                  :hide-details="!failed"
                  clearable
                  v-disabled-icon-focus
                ></v-text-field>
              </ValidationProvider>
            </v-col>
            <v-col cols="12" md="4">
              <ValidationProvider 
                v-slot="{ errors, failed }" 
                :name="contract.tenant_document_type==='cpf'?'CPF':'CNPJ'" 
                :rules="contract.tenant_document_type==='cpf'?'required|cpf':'required|cnpj'">
                <v-text-field
                  :label="contract.tenant_document_type==='cpf'?'CPF':'CNPJ'"
                  :placeholder="contract.tenant_document_type==='cpf'?'___.___.___-__':'__.___.___/____-__'"
                  outlined
                  v-model="contract.tenant_document"
                  :error-messages="errors"
                  :counter="255"
                  :hide-details="!failed"
                  clearable
                  v-disabled-icon-focus
                  v-mask="contract.tenant_document_type==='cpf'?'###.###.###-##':'##.###.###/####-##'"
                ></v-text-field>
              </ValidationProvider>
              </v-col>
            <v-col cols="12" md="4">
              <ValidationProvider v-slot="{ errors, failed }" name="EMAIL" rules="required|email">
                <v-text-field
                  label="E-mail do Contratante"
                  outlined
                  v-model="contract.tenant_email"
                  :error-messages="errors"
                  :hide-details="!failed"
                  clearable
                  v-disabled-icon-focus
                ></v-text-field>
              </ValidationProvider>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12"> 
              
              <v-btn color="primary" large @click="showDialogSelectProperty=true">
                <v-icon class="mr-2">mdi-home-city</v-icon>
                <span v-if="isEditMode">Alterar Imóvel</span>
                <span v-else>Vincular Imóvel</span>
              </v-btn>

              <div v-if="contract.property_id?false:true" class="ml-3 mt-1">
                <span class="error--text" style="font-size:12px;">
                  Necessário vincular um imóvel ao contrato  
                </span> 
              </div>

            </v-col>
            <v-col cols="12" v-if="contract.property">
              <v-simple-table dense>
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-center">Código</th>
                      <th class="text-left hidden-sm-and-down">Nome do proprietário</th>
                      <th class="text-left hidden-xs-only">Email do proprietário</th>
                      <th class="text-left">Endereço</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-center">{{ contract.property.id }}</td>
                      <td class="hidden-sm-and-down" >{{ contract.property.owner_name }}</td>
                      <td class="hidden-xs-only">{{ contract.property.owner_email }}</td>
                      <td>{{ utils.joinAddress(contract.property) }}</td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </v-col>
          </v-row>

        </v-card-text>
        
        <v-divider></v-divider>
        
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text color="red" @click="$router.back()">Cancelar</v-btn>
          <v-btn 
            type="submit"
            color="primary"
            :loading="loading"
          >
            <span v-if="isEditMode">Atualizar</span>
            <span v-else>Salvar</span>
          </v-btn>
        </v-card-actions>

      </v-form>

    </ValidationObserver>
        
        
    <v-dialog
      v-if="showDialogSelectProperty"
      v-model="showDialogSelectProperty"
      max-width="900"
      scrollable
    >
      <DialogSelectProperty 
        @close="showDialogSelectProperty=false" 
        @attach="attachPropertyInContract" 
      />
    </v-dialog>

  </v-card>
</template>

<script>

import { extend } from 'vee-validate';
import { isCNPJ, isCPF } from 'brazilian-values';
import DialogSelectProperty from '@/components/Contract/Dialog/SelectProperty'
import Swal from 'sweetalert2'

extend('cpf', value => {
  if (isCPF(value) ) {
    return true;
  }
  return 'Número de CPF inválido';
});

extend('cnpj', value => {
  if (isCNPJ(value)) {
    return true;
  }
  return 'Número de CNPJ inválido';
});

export default {

  components: { 
    DialogSelectProperty
  },

  inject: ['utils', 'contractRepository'],

  props: {
    isEditMode: {
      type: Boolean,
      default: false
    }
  },

  watch: {

  },

  data: () => ({
    
    showErrorButton: false,
    showDialogSelectProperty: false,

    contract: {
      tenant_document_type: 'cpf',
      tenant_document: null,
      tenant_name: null,
      tenant_email: null,
      property_id: null,
      property: null,
    },

    loading: false,

  }),

  created () {
    if (this.isEditMode) {
      this.getContract()
    }
  },

  methods: {

    submit() {
      this.$refs.observer.validate()
        .then( valid => {
          if (valid && this.contract.property_id ) {
            if (this.isEditMode) {
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Estamos trabalhando nesta funcionalidade!',
              })
            } else {
              this.storeContract()
            }
          }
        })
    },

    getContract() {

      let contractId = this.$route.params.id
      if (contractId) {
        this.contractRepository.getContract(contractId)
          .then((response) => {
            this.contract = response.data
          })
          .catch(() => this.$router.back())
      }
      
    },

    storeContract() {
      console.log('storeContract', this.contract)
      this.loading = true
      this.contractRepository.create(this.contract)
        .then(() => {
          this.loading = false
          this.$router.back()
        })
        .catch(() => this.loading = false)
    },

    attachPropertyInContract(property) {
      if (!property) return
      this.contract.property_id = property.id
      this.contract.property = property
      this.showDialogSelectProperty = false
    },
    
  }
};
</script>
