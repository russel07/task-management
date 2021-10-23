import { createApp } from 'vue'
import Toaster from '@meforma/vue-toaster';
import router from './router';

import App from './components/App.vue';
const app = createApp(App);
app.use(router);
app.use(Toaster);
app.mount('#wptms-app');