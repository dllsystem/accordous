import axios from 'axios';

const instance = axios.create({
  baseURL: 'http://localhost:80/api',
  withCredentials: false,
  headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
  }
})

export default instance;