<template>
  
  <v-card outlined class="ma-4" >
    <v-card-title primary-title>
      <span v-if="isEditMode">Editar Imóvel</span>
      <span v-else>Cadastrar Imóvel</span>
      <v-spacer></v-spacer>
    </v-card-title>
    <v-divider></v-divider>
    
    <ValidationObserver ref="observer">
      <v-form @submit.prevent="submit">
        <v-card-text>
          <v-row>
              <v-col cols="12" md="6">
                <ValidationProvider v-slot="{ errors, failed }" name="NOME" rules="required|min:3|max:255">
                  <v-text-field
                    label="Nome do Proprietário"
                    outlined
                    v-model="property.owner_name"
                    :error-messages="errors"
                    :counter="255"
                    :hide-details="!failed"
                    clearable
                    v-disabled-icon-focus
                  ></v-text-field>
                </ValidationProvider>
              </v-col>
              <v-col cols="12" md="6">
                <ValidationProvider v-slot="{ errors, failed }" name="EMAIL" rules="required|email">
                  <v-text-field
                    label="E-mail do Proprietário"
                    outlined
                    v-model="property.owner_email"
                    :error-messages="errors"
                    :hide-details="!failed"
                    clearable
                    v-disabled-icon-focus
                  ></v-text-field>
                </ValidationProvider>
              </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" sm="6" md="3">
              <v-text-field
                label="CEP"
                outlined
                @input="getCep"
                v-model="property.address_zipcode"
                @click:clear="clearAddress"
                maxlength="9"
                :loading="loading"
                v-mask="'#####-###'"
                placeholder="______-___"
                hide-details
                clearable
                v-disabled-icon-focus
              ></v-text-field>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" md="6" class="d-flex">
              <ValidationProvider class="w-100" v-slot="{ errors, failed }" name="RUA" rules="required|min:3|max:255">
                <v-text-field
                  label="Rua"
                  outlined
                  v-model="property.address_street"
                  :error-messages="errors"
                  :hide-details="!failed"
                  clearable
                  v-disabled-icon-focus
                ></v-text-field>
              </ValidationProvider>
              <v-text-field
                ref="textFieldAddressNumber"
                style="max-width:100px"
                label="Número"
                outlined
                class="ml-2"
                v-model="property.address_number"
                hide-details
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                label="Complemento"
                outlined
                v-model="property.address_complement"
                hide-details
                clearable
                v-disabled-icon-focus
              ></v-text-field>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" md="6">
              <ValidationProvider v-slot="{ errors, failed }" name="BAIRRO" rules="required|min:3|max:255">
                <v-text-field
                  label="Bairro"
                  outlined
                  v-model="property.address_neighborhood"
                  :error-messages="errors"
                  :hide-details="!failed"
                  clearable
                  v-disabled-icon-focus
                ></v-text-field>
              </ValidationProvider>
            </v-col>
            <v-col cols="12" md="6" class="d-flex">
              <ValidationProvider class="w-100" v-slot="{ errors, failed }" name="CIDADE" rules="required|min:3|max:255">
                <v-text-field
                  label="Cidade"
                  outlined
                  :error-messages="errors"
                  v-model="property.address_city"
                  :hide-details="!failed"
                  clearable
                  v-disabled-icon-focus
                ></v-text-field>
              </ValidationProvider>
              <ValidationProvider class="w-100" v-slot="{ errors, failed }" name="ESTADO" rules="required|min:2|max:255">
                <v-text-field
                  label="Estado"
                  outlined
                  class="ml-2"
                  :error-messages="errors"
                  v-model="property.address_state"
                  :hide-details="!failed"
                  clearable
                  v-disabled-icon-focus
                ></v-text-field>
              </ValidationProvider>
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
  </v-card>
</template>

<script>

import Swal from 'sweetalert2'

export default {

  components: { },

  inject: ['propertyRepository','cepRepository'],

  props: {
    isEditMode: {
      type: Boolean,
      default: false
    }
  },

  data: () => ({
    
    name: null,

    property: {
      address_success: false,
      address_zipcode: null,
      address_street: null,
      address_number: null,
      address_complement: null,
      address_neighborhood: null,
      address_city: null,
      address_state: null,
      owner_name: null,
      owner_email: null,
      is_rent: false,
    },

    loading: false,

  }),

  created () {
    if (this.isEditMode) {
      this.getProperty()
    }
  },

  methods: {

    submit() {
      this.$refs.observer.validate()
        .then( valid => {
          if (valid) {
            if (this.isEditMode) {
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Estamos trabalhando nesta funcionalidade!',
              })
            } else {
              this.storeProperty()
            }
          }
        })
    },

    getCep() {
      if (!this.property.address_zipcode || this.property.address_zipcode.replace('-','').length < 8) return
      this.loading = true
      this.cepRepository.getCep(this.property.address_zipcode)
          .then( response => {
            if (response.data) {
              if (!response.data.erro) {
                this.property.address_success = true
                this.property.address_zipcode = response.data.cep
                this.property.address_street = response.data.logradouro
                this.property.address_neighborhood = response.data.bairro
                this.property.address_city = response.data.localidade
                this.property.address_state = response.data.uf
                this.$refs.textFieldAddressNumber.focus()
              } else {
                this.property.address_success = false
              }
            }
            this.loading = false
          })
          .catch( () => {
            this.property.address_success = false
            this.loading = false
          })
    },

    clearAddress() {
      this.property.address_success = false
      this.property.address_zipcode = null
      this.property.address_street = null
      this.property.address_neighborhood = null
      this.property.address_city = null
      this.property.address_state = null
    },

    getProperty() {

      let propertyId = this.$route.params.id
      if (propertyId) {
        this.propertyRepository.getProperty(propertyId)
          .then((response) => {
            this.property = response.data
          })
          .catch(() => this.$router.back())
      }
      
    },

    storeProperty() {
      this.loading = true
      this.propertyRepository.create(this.property)
        .then(() => {
          this.loading = false
          this.$router.back()
        })
        .catch(() => this.loading = false)
    }
    
  }
};
</script>
