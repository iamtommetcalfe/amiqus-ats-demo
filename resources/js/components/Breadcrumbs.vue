<template>
  <nav
    class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700"
    aria-label="Breadcrumb"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <ol class="flex items-center space-x-4 h-10">
        <li v-for="(crumb, index) in breadcrumbs" :key="index" class="flex items-center">
          <div v-if="index > 0" class="flex-shrink-0 mx-2">
            <svg
              class="h-5 w-5 text-gray-400"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 20 20"
              fill="currentColor"
              aria-hidden="true"
            >
              <path
                fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <router-link
            v-if="!crumb.active"
            :to="crumb.path"
            class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
          >
            {{ crumb.name }}
          </router-link>
          <span v-else class="text-sm font-medium text-gray-900 dark:text-white">
            {{ crumb.name }}
          </span>
        </li>
      </ol>
    </div>
  </nav>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const _router = useRouter();
const breadcrumbs = ref([]);

// Function to get the job title from the API
const fetchJobTitle = async jobId => {
  try {
    const response = await axios.get(`/api/ats/jobs/${jobId}`);
    // Check for both old and new response structures
    if (
      response.data &&
      response.data.data &&
      response.data.data.job_posting &&
      response.data.data.job_posting.title
    ) {
      // New structure with data wrapper
      return response.data.data.job_posting.title;
    } else if (response.data && response.data.job_posting && response.data.job_posting.title) {
      // Old structure without data wrapper
      return response.data.job_posting.title;
    } else {
      console.warn('Job data structure not as expected:', response.data);
      return 'Job Details';
    }
  } catch (error) {
    console.error('Error fetching job title:', error);
    return 'Job Details';
  }
};

// Function to get the candidate name from the API
const fetchCandidateName = async candidateId => {
  try {
    const response = await axios.get(`/api/ats/candidates/${candidateId}`);
    // Add null checks to safely access candidate data
    if (
      response.data &&
      response.data.data &&
      response.data.data.candidate &&
      response.data.data.candidate.first_name &&
      response.data.data.candidate.last_name
    ) {
      return `${response.data.data.candidate.first_name} ${response.data.data.candidate.last_name}`;
    } else {
      console.warn('Candidate data structure not as expected:', response.data);
      return 'Candidate Details';
    }
  } catch (error) {
    console.error('Error fetching candidate name:', error);
    return 'Candidate Details';
  }
};

// Function to generate breadcrumbs based on current route
const generateBreadcrumbs = async () => {
  const currentRoute = route;
  const items = [];

  // Always add home as the first breadcrumb
  items.push({
    name: 'Home',
    path: '/',
    active: currentRoute.path === '/',
  });

  // Handle different routes
  if (currentRoute.name === 'background-checks') {
    items.push({
      name: 'Background Checks',
      path: '/background-checks',
      active: true,
    });
  } else if (currentRoute.name === 'integrations.settings') {
    items.push({
      name: 'Integration Settings',
      path: '/integrations/settings',
      active: true,
    });
  } else if (currentRoute.name === 'jobs.show' && currentRoute.params.id) {
    // Get job title for better context
    const jobTitle = await fetchJobTitle(currentRoute.params.id);
    items.push({
      name: jobTitle,
      path: `/jobs/${currentRoute.params.id}`,
      active: true,
    });
  } else if (currentRoute.name === 'candidates.show' && currentRoute.params.id) {
    // Check if we came from a job page
    const referrer = document.referrer;
    const jobMatch = referrer.match(/\/jobs\/(\d+)/);

    if (jobMatch && jobMatch[1]) {
      const jobId = jobMatch[1];
      const jobTitle = await fetchJobTitle(jobId);

      // Add job breadcrumb
      items.push({
        name: jobTitle,
        path: `/jobs/${jobId}`,
        active: false,
      });
    }

    // Get candidate name for better context
    const candidateName = await fetchCandidateName(currentRoute.params.id);
    items.push({
      name: candidateName,
      path: `/candidates/${currentRoute.params.id}`,
      active: true,
    });
  }

  return items;
};

// Update breadcrumbs when route changes
watch(
  () => route.path,
  async () => {
    breadcrumbs.value = await generateBreadcrumbs();
  },
  { immediate: true }
);
</script>
