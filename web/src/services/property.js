import api from '@/services/api'

const resource = '/property';

export default {
  get(query) {
    return api.get(`${resource}?${query}`);
  },
  getProperty(id) {
    return api.get(`${resource}/${id}`);
  },
  create(payload) {
    return api.post(`${resource}`, payload);
  },  
  update(payload, id) {
    return api.put(`${resource}/${id}`, payload);
  },
  delete(id) {
    return api.delete(`${resource}/${id}`);
  },
} 