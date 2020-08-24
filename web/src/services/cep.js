import axios from 'axios'

export default {
  getCep(cep) {
    return axios.get(`https://viacep.com.br/ws/${cep}/json/unicode/`);
  },
} 