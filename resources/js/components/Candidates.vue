<template>
  <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900 dark:text-gray-100">
      <h2 class="text-2xl font-semibold mb-4">Candidates</h2>

      <div v-if="loading" class="py-4">
        <p class="text-gray-500 dark:text-gray-400">Loading candidates...</p>
      </div>

      <div v-else-if="error" class="py-4">
        <p class="text-red-500">{{ error }}</p>
      </div>

      <div v-else-if="candidates.length === 0" class="py-4">
        <p class="text-gray-500 dark:text-gray-400">No candidates available at this time.</p>
      </div>

      <div v-else class="mt-4 grid gap-6 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="candidate in candidates"
          :key="candidate.id"
          class="bg-white dark:bg-gray-700 overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-300"
        >
          <div class="px-4 py-5 sm:p-6">
            <div class="flex justify-between items-start">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                {{ candidate.first_name }} {{ candidate.last_name }}
              </h3>
              <span
                v-if="candidate.isConnectedToAmiqus"
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100"
              >
                Amiqus Connected
              </span>
            </div>
            <div class="mt-2">
              <p class="text-sm text-gray-500 dark:text-gray-300">{{ candidate.email }}</p>
              <p v-if="candidate.phone" class="text-sm text-gray-500 dark:text-gray-300">
                {{ candidate.phone }}
              </p>
            </div>
            <div class="mt-2">
              <p
                v-if="candidate.current_position || candidate.current_company"
                class="text-sm text-gray-500 dark:text-gray-300"
              >
                {{ candidate.current_position || 'Not specified' }} at
                {{ candidate.current_company || 'Not specified' }}
              </p>
            </div>
            <div class="mt-4 flex justify-between items-center">
              <div class="text-sm">
                <p class="text-gray-500 dark:text-gray-300">
                  Added: {{ formatDate(candidate.created_at) }}
                </p>
              </div>
              <router-link
                :to="`/candidates/${candidate.id}`"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                View Details
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const candidates = ref([]);
const loading = ref(true);
const error = ref(null);

onMounted(async () => {
  try {
    const response = await axios.get('/api/ats/candidates');
    candidates.value = response.data.data;
  } catch (err) {
    error.value = 'Failed to load candidates. Please try again later.';
    console.error(err);
  } finally {
    loading.value = false;
  }
});

const formatDate = dateString => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-GB', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  });
};
</script>
