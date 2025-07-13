<template>
  <div class="relative mr-4">
    <div class="flex items-center">
      <input
        id="search-input"
        v-model="searchQuery"
        type="text"
        placeholder="Search candidates or jobs..."
        class="w-64 px-3 py-1.5 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-gray-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
        @focus="showResults = true"
        @keyup="debounceSearch"
      />
      <button
        v-if="searchQuery"
        class="absolute right-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
        @click="clearSearch"
      >
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M6 18L18 6M6 6l12 12"
          />
        </svg>
      </button>
    </div>

    <!-- Search Results Dropdown -->
    <div
      v-if="showResults && (searchQuery || isLoading || error)"
      v-click-outside="hideResults"
      class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg rounded-md overflow-hidden"
      style="max-height: 400px; overflow-y: auto"
    >
      <!-- Loading State -->
      <div v-if="isLoading" class="p-4 text-center text-gray-700 dark:text-gray-400">
        <svg
          class="animate-spin h-5 w-5 mx-auto mb-2 text-gray-700 dark:text-gray-400"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
        >
          <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
          ></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>
        <p>Searching...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="p-4 text-center text-red-500">
        {{ error }}
      </div>

      <!-- Empty State -->
      <div
        v-else-if="searchQuery && !candidates.length && !jobs.length"
        class="p-4 text-center text-gray-700 dark:text-gray-400"
      >
        No results found for "{{ searchQuery }}"
      </div>

      <!-- Results -->
      <div v-else>
        <!-- Candidates Section -->
        <div v-if="candidates.length > 0">
          <div
            class="search-results-heading px-4 py-2 bg-gray-50 dark:bg-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider"
          >
            Candidates
          </div>
          <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <router-link
              v-for="candidate in candidates"
              :key="'candidate-' + candidate.id"
              :to="`/candidates/${candidate.id}`"
              class="block px-4 py-2 hover:bg-blue-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 transition duration-150 ease-in-out"
              @click="hideResults"
            >
              <div class="text-sm font-medium text-gray-900 dark:text-white">
                {{ candidate.first_name }} {{ candidate.last_name }}
              </div>
              <div class="text-xs text-gray-700 dark:text-gray-400">
                {{ candidate.email }}
              </div>
              <div
                v-if="candidate.current_position || candidate.current_company"
                class="text-xs text-gray-700 dark:text-gray-400"
              >
                {{ candidate.current_position || '' }}
                {{ candidate.current_position && candidate.current_company ? 'at' : '' }}
                {{ candidate.current_company || '' }}
              </div>
            </router-link>
          </div>
        </div>

        <!-- Jobs Section -->
        <div v-if="jobs.length > 0">
          <div
            class="search-results-heading px-4 py-2 bg-gray-50 dark:bg-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider"
          >
            Jobs
          </div>
          <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <router-link
              v-for="job in jobs"
              :key="'job-' + job.id"
              :to="`/jobs/${job.id}`"
              class="block px-4 py-2 hover:bg-blue-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 transition duration-150 ease-in-out"
              @click="hideResults"
            >
              <div class="text-sm font-medium text-gray-900 dark:text-white">
                {{ job.title }}
              </div>
              <div class="text-xs text-gray-700 dark:text-gray-400">
                {{ job.department }} · {{ job.location }}
              </div>
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

// State
const searchQuery = ref('');
const showResults = ref(false);
const isLoading = ref(false);
const error = ref(null);
const candidates = ref([]);
const jobs = ref([]);
let searchTimeout = null;

// Methods
const search = async () => {
  if (!searchQuery.value.trim()) {
    candidates.value = [];
    jobs.value = [];
    return;
  }

  isLoading.value = true;
  error.value = null;

  try {
    const response = await axios.get('/api/search', {
      params: { query: searchQuery.value },
    });

    // Access the nested data structure from the API response
    candidates.value = response.data.data?.candidates || [];
    jobs.value = response.data.data?.jobs || [];
  } catch (err) {
    console.error('Search error:', err);
    error.value = 'Failed to perform search. Please try again.';
  } finally {
    isLoading.value = false;
  }
};

const debounceSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    search();
  }, 300);
};

const clearSearch = () => {
  searchQuery.value = '';
  candidates.value = [];
  jobs.value = [];
  error.value = null;
};

const hideResults = () => {
  showResults.value = false;
};

// Click outside directive
const vClickOutside = {
  mounted(el, binding) {
    el._clickOutside = event => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value(event);
      }
    };
    document.addEventListener('click', el._clickOutside);
  },
  unmounted(el) {
    document.removeEventListener('click', el._clickOutside);
  },
};
</script>
