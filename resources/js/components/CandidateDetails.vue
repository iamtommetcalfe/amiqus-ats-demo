<template>
  <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900 dark:text-gray-100">
      <div v-if="loading" class="py-4">
        <p class="text-gray-500 dark:text-gray-400">Loading candidate details...</p>
      </div>

      <div v-else-if="error" class="py-4">
        <p class="text-red-500">{{ error }}</p>
      </div>

      <div v-else>
        <!-- Candidate Header -->
        <div class="border-b border-gray-200 dark:border-gray-700 pb-5">
          <div class="flex justify-between items-start">
            <div>
              <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ candidate.first_name }} {{ candidate.last_name }}</h2>
              <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-300">
                  <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                  </svg>
                  {{ candidate.email }}
                </div>
                <div v-if="candidate.phone" class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-300">
                  <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                  </svg>
                  {{ candidate.phone }}
                </div>
              </div>
            </div>

            <!-- Amiqus Buttons and Application Count -->
            <div class="flex items-center">
              <span v-if="applications.length > 0" class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 mr-4">
                {{ applications.length }} {{ applications.length === 1 ? 'Application' : 'Applications' }}
              </span>

              <!-- Create Person in Amiqus button -->
              <button
                v-if="!amiqus.is_connected"
                @click="createAmiqusClient"
                :disabled="isCreatingAmiqusClient"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 cursor-pointer"
              >
                <svg v-if="isCreatingAmiqusClient" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isCreatingAmiqusClient ? 'Creating...' : 'Create Person in Amiqus' }}
              </button>

              <!-- View in Amiqus button -->
              <a
                v-else
                :href="amiqus.client_url"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 cursor-pointer"
              >
                View in Amiqus
                <svg class="ml-2 -mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                  <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                </svg>
              </a>
            </div>
          </div>

          <!-- Current Position -->
          <div v-if="candidate.current_position || candidate.current_company" class="mt-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Current Position</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">
              {{ candidate.current_position || 'Not specified' }} at {{ candidate.current_company || 'Not specified' }}
            </p>
          </div>

          <!-- Success and Error Messages -->
          <div v-if="amiqusClientCreationSuccess || amiqusClientCreationError" class="mt-4">
            <!-- Success message -->
            <div v-if="amiqusClientCreationSuccess" class="p-2 bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 rounded">
              Person created successfully in Amiqus!
            </div>

            <!-- Error message -->
            <div v-if="amiqusClientCreationError" class="p-2 bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100 rounded">
              {{ amiqusClientCreationError }}
            </div>
          </div>

          <!-- Source -->
          <div v-if="candidate.source" class="mt-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Source</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">
              {{ candidate.source }}
            </p>
          </div>

          <!-- Notes -->
          <div v-if="candidate.notes" class="mt-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Notes</h3>
            <div class="mt-1 text-sm text-gray-500 dark:text-gray-300 whitespace-pre-line">
              {{ candidate.notes }}
            </div>
          </div>
        </div>

        <!-- Applications Section -->
        <div class="mt-6">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">Job Applications</h3>

          <div v-if="applications.length === 0" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            No job applications found for this candidate.
          </div>

          <div v-else class="mt-4 space-y-6">
            <div v-for="(application, index) in applications" :key="index" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
              <div class="flex justify-between items-start">
                <div>
                  <router-link
                    :to="`/jobs/${application.job_posting.id}`"
                    class="text-md font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                  >
                    {{ application.job_posting.title }}
                  </router-link>
                  <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ application.job_posting.department }} · {{ application.job_posting.location }}
                  </p>
                  <div class="mt-2 flex items-center">
                    <div class="w-3 h-3 rounded-full mr-2" :style="{ backgroundColor: application.interview_stage.color }"></div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ application.interview_stage.name }}
                    </span>
                  </div>
                  <p v-if="application.scheduled_at" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-medium">Scheduled:</span> {{ formatDateTime(application.scheduled_at) }}
                  </p>
                  <p v-if="application.notes" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-medium">Notes:</span> {{ application.notes }}
                  </p>
                  <p v-if="application.feedback" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-medium">Feedback:</span> {{ application.feedback }}
                  </p>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="getStatusClass(application.status)">
                  {{ application.status }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, defineProps } from 'vue';
import axios from 'axios';

const props = defineProps({
  id: {
    type: String,
    required: true
  }
});

const candidate = ref({});
const applications = ref([]);
const loading = ref(true);
const error = ref(null);
const amiqus = ref({ is_connected: false, client_url: null });
const isCreatingAmiqusClient = ref(false);
const amiqusClientCreationError = ref(null);
const amiqusClientCreationSuccess = ref(false);

onMounted(async () => {
  try {
    if (!props.id) {
      error.value = 'Candidate ID is missing. Please go back to the job listings.';
      loading.value = false;
      return;
    }

    const response = await axios.get(`/api/ats/candidates/${props.id}`);
    candidate.value = response.data.candidate;
    applications.value = response.data.applications;

    // Set Amiqus client information
    if (response.data.amiqus) {
      amiqus.value = response.data.amiqus;
    }
  } catch (err) {
    error.value = 'Failed to load candidate details. Please try again later.';
    console.error(err);
  } finally {
    loading.value = false;
  }
});

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

/**
 * Create a client in Amiqus and link it to the candidate.
 */
const createAmiqusClient = async () => {
  // Reset state
  amiqusClientCreationError.value = null;
  amiqusClientCreationSuccess.value = false;
  isCreatingAmiqusClient.value = true;

  try {
    const response = await axios.post(`/api/ats/candidates/${props.id}/amiqus-client`, {
      title: 'mr', // Default title, could be made configurable
    });

    if (response.data.success) {
      // Update local state
      amiqus.value.is_connected = true;
      amiqus.value.client_url = response.data.amiqus_client_url;
      amiqusClientCreationSuccess.value = true;

      // Update candidate data
      candidate.value = response.data.candidate;
    } else {
      amiqusClientCreationError.value = response.data.message || 'Failed to create Amiqus client.';
    }
  } catch (err) {
    console.error('Error creating Amiqus client:', err);
    amiqusClientCreationError.value = err.response?.data?.message || 'Failed to create Amiqus client. Please try again.';
  } finally {
    isCreatingAmiqusClient.value = false;
  }
};
</script>
