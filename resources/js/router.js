import { createRouter, createWebHistory } from 'vue-router';
import Home from './components/Home.vue';
import IntegrationsSettings from './components/IntegrationsSettings.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: Home
    },
    {
        path: '/integrations/settings',
        name: 'integrations.settings',
        component: IntegrationsSettings
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
