import './bootstrap';

import { createApp } from 'vue';
import App from '@/js/src/App.vue';

import '@/assets/css/portal.css';
import '@/assets/plugins/popper.min.js';
import '@/assets/plugins/fontawesome/js/all.min.js';
import '@/assets/plugins/bootstrap/js/bootstrap.min.js';

import { createPinia } from 'pinia'
import {useUserStore} from "@/js/src/stores/UserStore";
import Router from '@/js/src/router/router.js';
import {RouteNamesEnum} from "./src/router/RouteNamesEnum";

const app = createApp(App);
app.use(createPinia());
app.use(Router);


window.axios.interceptors.response.use({}, err => {
    if (err.response.status === 401 || err.response.status === 419) {
        const userStore = useUserStore();
        if (userStore.isAuthenticated) {
            userStore.logOut();
            userStore.$reset();
        }
        router.push({ name: RouteNamesEnum.USER_LOGIN })
    }

    return err.response;
});

app.mount('#app');
