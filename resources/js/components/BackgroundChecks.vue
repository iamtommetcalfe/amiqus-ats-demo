<template>
  <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900 dark:text-gray-100">
      <h2 class="text-2xl font-semibold mb-4">Background Checks</h2>

      <div v-if="loading" class="py-4">
        <p class="text-gray-500 dark:text-gray-400">Loading background checks...</p>
      </div>

      <div v-else-if="error" class="py-4">
        <p class="text-red-500">{{ error }}</p>
      </div>

      <!-- Success message -->
      <div v-if="backgroundCheckSyncSuccess" class="mb-4 p-2 bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 rounded flex justify-between items-center">
        <span>{{ backgroundCheckSyncSuccess }}</span>
        <button @click="backgroundCheckSyncSuccess = null" class="text-green-800 dark:text-green-100 hover:text-green-600 dark:hover:text-green-300 focus:outline-none">
          <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>

      <!-- Error message -->
      <div v-if="backgroundCheckSyncError" class="mb-4 p-2 bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100 rounded flex justify-between items-center">
        <span>{{ backgroundCheckSyncError }}</span>
        <button @click="backgroundCheckSyncError = null" class="text-red-800 dark:text-red-100 hover:text-red-600 dark:hover:text-red-300 focus:outline-none">
          <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>

      <div v-else-if="backgroundChecks.length === 0" class="py-4">
        <p class="text-gray-500 dark:text-gray-400">No background checks found.</p>
      </div>

      <div v-else class="mt-4 space-y-4">
        <div v-for="check in backgroundChecks" :key="check.id" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
          <div class="flex justify-between items-start">
            <div>
              <h4 class="text-md font-medium text-gray-900 dark:text-white">{{ check.template_name }}</h4>
              <router-link
                :to="`/candidates/${check.candidate.id}`"
                class="mt-1 text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
              >
                {{ check.candidate.first_name }} {{ check.candidate.last_name }}
              </router-link>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ check.candidate.email }}
              </p>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Cost: {{ Math.round(check.cost) }} {{ Math.round(check.cost) === 1 ? 'credit' : 'credits' }}
              </p>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Created: {{ formatDateTime(check.created_at) }}
              </p>
              <p v-if="check.expires_at" class="text-sm text-gray-500 dark:text-gray-400">
                Expires: {{ formatDateTime(check.expires_at) }}
              </p>
              <p v-if="check.completed_at" class="text-sm text-gray-500 dark:text-gray-400">
                Completed: {{ formatDateTime(check.completed_at) }}
              </p>
            </div>
            <div class="flex flex-col items-end space-y-2">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="getStatusClass(check.status)">
                {{ check.status }}
              </span>
              <div class="flex space-x-2">
                <button
                  @click="syncBackgroundCheck(check.candidate.id, check.id)"
                  :disabled="syncingBackgroundChecks[check.id]"
                  class="inline-flex items-center text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg v-if="syncingBackgroundChecks[check.id]" class="animate-spin -ml-1 mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                  {{ syncingBackgroundChecks[check.id] ? 'Syncing...' : 'Sync' }}
                </button>
                <a
                  v-if="check.amiqus_record_url"
                  :href="check.amiqus_record_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                >
                  View in Amiqus
                  <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                    <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                  </svg>
                </a>
              </div>
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

// State
const backgroundChecks = ref([]);
const loading = ref(true);
const error = ref(null);
const syncingBackgroundChecks = ref({});
const backgroundCheckSyncSuccess = ref(null);
const backgroundCheckSyncError = ref(null);

// Fetch all background checks
const fetchBackgroundChecks = async () => {
  loading.value = true;
  error.value = null;

  try {
    const response = await axios.get('/api/ats/background-checks');

    if (response.data.success && response.data.data && response.data.data.background_checks) {
      backgroundChecks.value = response.data.data.background_checks;
    } else {
      console.warn('Unexpected API response structure:', response.data);
      error.value = 'Failed to load background checks. Unexpected API response structure.';
    }
  } catch (err) {
    console.error('Error fetching background checks:', err);
    error.value = 'Failed to load background checks. Please try again later.';
  } finally {
    loading.value = false;
  }
};

// Format date and time
const formatDateTime = (dateTimeString) => {
  if (!dateTimeString) return 'N/A';
  const date = new Date(dateTimeString);
  return date.toLocaleDateString('en-GB', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Get CSS class for status badge
const getStatusClass = (status) => {
  switch (status) {
    case 'pending':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100';
    case 'scheduled':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100';
    case 'completed':
      return 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100';
    case 'cancelled':
      return 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100';
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100';
  }
};

// Sync a background check with the Amiqus API
const syncBackgroundCheck = async (candidateId, backgroundCheckId) => {
  // Set syncing state for this specific background check
  syncingBackgroundChecks.value[backgroundCheckId] = true;

  // Reset success/error messages
  backgroundCheckSyncSuccess.value = null;
  backgroundCheckSyncError.value = null;

  try {
    const response = await axios.post(`/api/ats/candidates/${candidateId}/background-checks/${backgroundCheckId}/sync`);

    if (response.data.success) {
      // Update the background check in the list
      const index = backgroundChecks.value.findIndex(check => check.id === backgroundCheckId);
      if (index !== -1) {
        // Preserve the candidate information when updating
        const candidate = backgroundChecks.value[index].candidate;
        backgroundChecks.value[index] = {
          ...response.data.background_check,
          candidate
        };
      }

      // Show success message
      backgroundCheckSyncSuccess.value = 'Background check synced successfully.';

      // Hide success message after 3 seconds
      setTimeout(() => {
        backgroundCheckSyncSuccess.value = null;
      }, 3000);
    } else {
      backgroundCheckSyncError.value = response.data.message || 'Failed to sync background check.';
    }
  } catch (err) {
    console.error('Error syncing background check:', err);
    backgroundCheckSyncError.value = err.response?.data?.message || 'Failed to sync background check. Please try again.';
  } finally {
    // Clear syncing state
    syncingBackgroundChecks.value[backgroundCheckId] = false;
  }
};

// Fetch background checks on component mount
onMounted(fetchBackgroundChecks);
</script>
