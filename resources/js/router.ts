import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import Home from './components/Home.vue';
import BackgroundChecks from './components/BackgroundChecks.vue';
import IntegrationsSettings from './components/IntegrationsSettings.vue';
import JobDetails from './components/JobDetails.vue';
import CandidateDetails from './components/CandidateDetails.vue';
import Candidates from './components/Candidates.vue';

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    name: 'home',
    component: Home,
  },
  {
    path: '/background-checks',
    name: 'background-checks',
    component: BackgroundChecks,
  },
  {
    path: '/candidates',
    name: 'candidates',
    component: Candidates,
  },
  {
    path: '/integrations/settings',
    name: 'integrations.settings',
    component: IntegrationsSettings,
  },
  {
    path: '/jobs/:id',
    name: 'jobs.show',
    component: JobDetails,
    props: true,
  },
  {
    path: '/candidates/:id',
    name: 'candidates.show',
    component: CandidateDetails,
    props: true,
  },
  // Catch-all route for 404 errors
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
