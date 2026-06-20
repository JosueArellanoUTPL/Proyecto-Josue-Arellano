import axios from 'axios';

// Configurar solicitudes HTTP.
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
