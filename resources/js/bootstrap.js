import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */


import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Ulises = "fsdfsdf"
window.Pusher = Pusher;


window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,  // Usa el valor de la clave de tu app Pusher del .env
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,  // Usa el valor del clúster de tu app Pusher
    forceTLS: true,  // Si estás usando HTTPS en tu aplicación
    disableStats: true,
    // broadcaster: 'pusher',
    // key: '',  // No necesitas usar el APP_KEY si estás usando Soketi, puedes usarlo como string vacío
    // wsHost: window.location.hostname,  // Configura el hostname de tu servidor
    // wsPort: 6001,  // Este es el puerto que Soketi expone
    // forceTLS: false,
    // disableStats: true,
});
//si estás en la nube, configura wsHost para que apunte a la URL de tu servidor de Soketi.


window.PUSHER_APP_KEY = import.meta.env.VITE_PUSHER_APP_KEY;
window.PUSHER_APP_CLUSTER = import.meta.env.VITE_PUSHER_APP_CLUSTER;
