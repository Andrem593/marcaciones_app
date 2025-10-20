import axios from 'axios';

axios.defaults.withCredentials = true;
axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL || 'http://public.test:8080'; // Asegúrate de ajustar la URL base según tu configuración

export default axios;