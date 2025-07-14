<template>
  <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900 dark:text-gray-100">
      <div v-if="loading" class="py-4">
        <p class="text-gray-500 dark:text-gray-400">Loading job details...</p>
      </div>

      <div v-else-if="error" class="py-4">
        <p class="text-red-500">{{ error }}</p>
      </div>

      <div v-else>
        <!-- Job Header -->
        <div class="border-b border-gray-200 dark:border-gray-700 pb-5">
          <div class="flex justify-between items-start">
            <div>
              <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ job.title }}</h2>
              <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-300">
                  <svg
                    class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400 dark:text-gray-500"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  {{ job.location }}
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-300">
                  <svg
                    class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400 dark:text-gray-500"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                  >
                    <path
                      d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"
                    />
                  </svg>
                  {{ job.department }}
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-300">
                  <svg
                    class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400 dark:text-gray-500"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
                      clip-rule="evenodd"
                    />
                    <path
                      d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"
                    />
                  </svg>
                  {{ job.employment_type }}
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-300">
                  <svg
                    class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400 dark:text-gray-500"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Posted: {{ formatDate(job.posted_at) }} | Closes: {{ formatDate(job.closes_at) }}
                </div>
              </div>
            </div>
            <span
              class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100"
            >
              {{ totalApplicants }} {{ totalApplicants === 1 ? 'Applicant' : 'Applicants' }}
            </span>
          </div>

          <!-- Salary Range -->
          <div class="mt-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Salary Range</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">
              {{ formatSalary(job.salary_min) }} - {{ formatSalary(job.salary_max) }}
            </p>
          </div>

          <!-- Job Description -->
          <div class="mt-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Job Description</h3>
            <div class="mt-1 text-sm text-gray-500 dark:text-gray-300 whitespace-pre-line">
              {{ job.description }}
            </div>
          </div>
        </div>

        <!-- Tabbed Section -->
        <div class="mt-6">
          <!-- Tab Navigation with Horizontal Scrolling -->
          <div class="border-b border-gray-200 dark:border-gray-700 relative">
            <!-- Left scroll arrow -->
            <button
              v-if="canScrollLeft"
              class="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 bg-white dark:bg-gray-800 rounded-full shadow-md p-1 focus:outline-none"
              aria-label="Scroll left"
              @click="scrollLeft"
            >
              <svg
                class="h-6 w-6 text-gray-500 dark:text-gray-400"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 19l-7-7 7-7"
                />
              </svg>
            </button>

            <!-- Scrollable tabs container -->
            <div ref="tabsContainer" class="overflow-hidden">
              <nav
                ref="tabsNav"
                class="flex -mb-px space-x-8 overflow-x-auto scrollbar-hide py-1 px-8 no-scrollbar"
                aria-label="Tabs"
              >
                <a
                  v-for="stageId in Object.keys(applicantsByStage)"
                  :key="stageId"
                  :href="`#${stageId}`"
                  :class="[
                    activeTab === stageId
                      ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-gray-600',
                    'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex-shrink-0',
                  ]"
                  @click.prevent="setActiveTab(stageId)"
                >
                  <div class="flex items-center">
                    <div
                      class="w-3 h-3 rounded-full mr-2"
                      :style="{ backgroundColor: applicantsByStage[stageId].stage.color }"
                    ></div>
                    {{ applicantsByStage[stageId].stage.name }}
                    <span
                      :class="[
                        activeTab === stageId
                          ? 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-400'
                          : 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-gray-300',
                        'ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium',
                      ]"
                    >
                      {{ applicantsByStage[stageId].applicants.length }}
                    </span>
                  </div>
                </a>
              </nav>
            </div>

            <!-- Right scroll arrow -->
            <button
              v-if="canScrollRight"
              class="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 bg-white dark:bg-gray-800 rounded-full shadow-md p-1 focus:outline-none"
              aria-label="Scroll right"
              @click="scrollRight"
            >
              <svg
                class="h-6 w-6 text-gray-500 dark:text-gray-400"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5l7 7-7 7"
                />
              </svg>
            </button>
          </div>

          <!-- Tab Content -->
          <div class="mt-4">
            <div
              v-for="(stageData, stageId) in applicantsByStage"
              v-show="activeTab === stageId"
              :key="stageId"
            >
              <div
                v-if="stageData.applicants.length === 0"
                class="mt-2 text-sm text-gray-500 dark:text-gray-400"
              >
                No applicants in this stage
              </div>

              <div v-else class="mt-2 space-y-4">
                <div
                  v-for="applicant in stageData.applicants"
                  :key="applicant.id"
                  class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg"
                >
                  <div class="flex justify-between items-start">
                    <div>
                      <h5 class="text-sm font-medium text-gray-900 dark:text-white">
                        <router-link
                          :to="{ name: 'candidates.show', params: { id: applicant.candidate.id } }"
                          class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                        >
                          {{ applicant.candidate.first_name }} {{ applicant.candidate.last_name }}
                        </router-link>
                      </h5>
                      <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ applicant.candidate.email }} |
                        {{ applicant.candidate.phone || 'No phone' }}
                      </p>
                      <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ applicant.candidate.current_position }} at
                        {{ applicant.candidate.current_company }}
                      </p>
                      <p
                        v-if="applicant.scheduled_at"
                        class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                      >
                        <span class="font-medium">Scheduled:</span>
                        {{ formatDateTime(applicant.scheduled_at) }}
                      </p>
                      <p
                        v-if="applicant.notes"
                        class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                      >
                        <span class="font-medium">Notes:</span> {{ applicant.notes }}
                      </p>
                    </div>
                    <span
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="getStatusClass(applicant.status)"
                    >
                      {{ applicant.status }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, defineProps, watch } from 'vue';
import axios from 'axios';
import { useRoute } from 'vue-router';

const route = useRoute();
const props = defineProps({
  id: {
    type: String,
    required: true,
  },
});

const job = ref({});
const applicantsByStage = ref({});
const loading = ref(true);
const error = ref(null);
const activeTab = ref('');

// Refs for tab scrolling
const tabsContainer = ref(null);
const tabsNav = ref(null);
const canScrollLeft = ref(false);
const canScrollRight = ref(false);

// Store reference to the hash change handler for proper cleanup
const handleHashChange = () => {
  if (window.location.hash) {
    const hash = window.location.hash.substring(1);
    if (Object.keys(applicantsByStage.value).includes(hash)) {
      activeTab.value = hash;
    }
  } else if (Object.keys(applicantsByStage.value).length > 0) {
    // Default to first stage if no hash
    activeTab.value = Object.keys(applicantsByStage.value)[0];
  }
};

const totalApplicants = computed(() => {
  let count = 0;
  for (const stageId in applicantsByStage.value) {
    count += applicantsByStage.value[stageId].applicants.length;
  }
  return count;
});

/**
 * Set the active tab and update the URL hash.
 */
const setActiveTab = tabId => {
  activeTab.value = tabId;
  // Update the URL hash without triggering a page reload
  window.history.pushState(null, null, `#${tabId}`);

  // Scroll the tab into view if needed
  // We need to wait for the DOM to update
  setTimeout(() => {
    const activeTabElement = document.querySelector(`a[href="#${tabId}"]`);
    if (activeTabElement && tabsNav.value) {
      // Check if the tab is not fully visible
      const tabRect = activeTabElement.getBoundingClientRect();
      const containerRect = tabsNav.value.getBoundingClientRect();

      // If the tab is not fully visible, scroll it into view
      if (tabRect.left < containerRect.left || tabRect.right > containerRect.right) {
        activeTabElement.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        // Update scroll indicators after scrolling
        setTimeout(checkScrollPosition, 300);
      }
    }
  }, 0);
};

/**
 * Scroll the tabs to the left
 */
const scrollLeft = () => {
  if (tabsNav.value) {
    tabsNav.value.scrollBy({ left: -200, behavior: 'smooth' });
    // Check scroll position after animation
    setTimeout(checkScrollPosition, 300);
  }
};

/**
 * Scroll the tabs to the right
 */
const scrollRight = () => {
  if (tabsNav.value) {
    tabsNav.value.scrollBy({ left: 200, behavior: 'smooth' });
    // Check scroll position after animation
    setTimeout(checkScrollPosition, 300);
  }
};

/**
 * Check if we can scroll left or right
 */
const checkScrollPosition = () => {
  if (tabsNav.value) {
    // Can scroll left if scrollLeft > 0
    canScrollLeft.value = tabsNav.value.scrollLeft > 0;

    // Can scroll right if scrollLeft + clientWidth < scrollWidth
    canScrollRight.value =
      tabsNav.value.scrollLeft + tabsNav.value.clientWidth < tabsNav.value.scrollWidth - 5; // 5px buffer
  }
};

onMounted(async () => {
  try {
    // Use either props.id or route.params.id, whichever is available
    const jobId = props.id || route.params.id;

    if (!jobId) {
      error.value = 'Job ID is missing. Please go back to the job listings.';
      loading.value = false;
      return;
    }

    // Ensure jobId is a string
    const response = await axios.get(`/api/ats/jobs/${String(jobId)}`);
    job.value = response.data.job_posting;
    applicantsByStage.value = response.data.applicants_by_stage;

    // Set the initial active tab based on the URL hash or default to the first stage
    handleHashChange();

    // Add event listener for hash changes to support browser back/forward navigation
    window.addEventListener('hashchange', handleHashChange);

    // Check scroll position after the component is mounted and tabs are rendered
    setTimeout(checkScrollPosition, 100);

    // Add event listener for window resize to update scroll indicators
    window.addEventListener('resize', checkScrollPosition);

    // Add event listener for scroll events on the tabs container
    if (tabsNav.value) {
      tabsNav.value.addEventListener('scroll', checkScrollPosition);
    }
  } catch (err) {
    error.value = 'Failed to load job details. Please try again later.';
    console.error(err);
  } finally {
    loading.value = false;
  }
});

// Watch for changes in applicantsByStage to update scroll indicators
watch(
  applicantsByStage,
  () => {
    // Wait for DOM to update
    setTimeout(checkScrollPosition, 100);
  },
  { deep: true }
);

onUnmounted(() => {
  // Remove event listeners to prevent memory leaks
  window.removeEventListener('hashchange', handleHashChange);
  window.removeEventListener('resize', checkScrollPosition);

  // Remove scroll event listener from tabs container
  if (tabsNav.value) {
    tabsNav.value.removeEventListener('scroll', checkScrollPosition);
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

const formatDateTime = dateTimeString => {
  if (!dateTimeString) return 'N/A';
  const date = new Date(dateTimeString);
  return date.toLocaleDateString('en-GB', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const formatSalary = amount => {
  if (!amount) return 'N/A';
  return new Intl.NumberFormat('en-GB', {
    style: 'currency',
    currency: 'GBP',
    maximumFractionDigits: 0,
  }).format(amount);
};

const getStatusClass = status => {
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
</script>

<style scoped>
/* Hide scrollbar for Chrome, Safari and Opera */
.no-scrollbar::-webkit-scrollbar {
  display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
.no-scrollbar {
  -ms-overflow-style: none; /* IE and Edge */
  scrollbar-width: none; /* Firefox */
}
</style>
