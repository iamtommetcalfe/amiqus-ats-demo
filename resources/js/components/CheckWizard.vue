<template>
  <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
      <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Check Wizard</h3>
      <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
        Create a background check for a candidate
      </p>
    </div>

    <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-5 sm:p-6">
      <!-- Step indicator -->
      <div class="mb-8">
        <div class="flex items-center">
          <div
            class="flex items-center justify-center w-10 h-10 rounded-full"
            :class="[
              currentStep >= 1
                ? 'bg-indigo-600 text-white'
                : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
            ]"
          >
            1
          </div>
          <div
            class="flex-1 h-1 mx-2"
            :class="[currentStep >= 2 ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700']"
          ></div>
          <div
            class="flex items-center justify-center w-10 h-10 rounded-full"
            :class="[
              currentStep >= 2
                ? 'bg-indigo-600 text-white'
                : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
            ]"
          >
            2
          </div>
        </div>
        <div class="flex justify-between mt-2">
          <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Select Candidate</div>
          <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Select Template</div>
        </div>
      </div>

      <!-- Step 1: Select Candidate -->
      <div v-if="currentStep === 1">
        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
          Step 1: Select a Candidate
        </h4>
        <div class="mb-4">
          <label
            for="candidate-search"
            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
            >Search for a candidate</label
          >
          <div class="mt-1 relative">
            <input
              id="candidate-search"
              v-model="candidateSearch"
              type="text"
              placeholder="Start typing to search..."
              class="block w-full px-3 py-1.5 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-gray-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              @input="debounceSearchCandidates"
            />
            <div
              v-if="isLoadingCandidates"
              class="absolute inset-y-0 right-0 pr-3 flex items-center"
            >
              <svg
                class="animate-spin h-5 w-5 text-gray-400"
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
            </div>
          </div>

          <!-- Candidate search results -->
          <div
            v-if="candidateSearch && candidates.length > 0"
            class="mt-1 bg-white dark:bg-gray-800 shadow-lg rounded-md overflow-hidden"
          >
            <ul class="divide-y divide-gray-200 dark:divide-gray-700 max-h-60 overflow-y-auto">
              <li
                v-for="candidate in candidates"
                :key="candidate.id"
                class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                @click="selectCandidate(candidate)"
              >
                <div class="flex items-center">
                  <div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ candidate.first_name }} {{ candidate.last_name }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ candidate.email }}
                    </div>
                    <div
                      v-if="candidate.current_position || candidate.current_company"
                      class="text-sm text-gray-500 dark:text-gray-400"
                    >
                      {{ candidate.current_position || '' }}
                      {{ candidate.current_position && candidate.current_company ? 'at' : '' }}
                      {{ candidate.current_company || '' }}
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>

          <div
            v-else-if="candidateSearch && !isLoadingCandidates && candidates.length === 0"
            class="mt-1 p-4 bg-white dark:bg-gray-800 shadow-lg rounded-md text-center text-gray-500 dark:text-gray-400"
          >
            No candidates found
          </div>
        </div>

        <!-- Selected candidate -->
        <div v-if="selectedCandidate" class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
          <div class="flex justify-between items-start">
            <div>
              <h5 class="text-lg font-medium text-gray-900 dark:text-white">
                {{ selectedCandidate.first_name }} {{ selectedCandidate.last_name }}
              </h5>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ selectedCandidate.email }}
              </p>
              <p
                v-if="selectedCandidate.current_position || selectedCandidate.current_company"
                class="text-sm text-gray-500 dark:text-gray-400"
              >
                {{ selectedCandidate.current_position || '' }}
                {{
                  selectedCandidate.current_position && selectedCandidate.current_company
                    ? 'at'
                    : ''
                }}
                {{ selectedCandidate.current_company || '' }}
              </p>
            </div>
            <button
              type="button"
              class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
              @click="clearSelectedCandidate"
            >
              <span class="sr-only">Clear</span>
              <svg
                class="h-5 w-5"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
              >
                <path
                  fill-rule="evenodd"
                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Step 2: Select Template -->
      <div v-if="currentStep === 2">
        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
          Step 2: Select a Request Template
        </h4>
        <div class="mb-4">
          <div v-if="isLoadingTemplates" class="text-center py-4">
            <svg
              class="animate-spin h-8 w-8 mx-auto text-indigo-500"
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
            <p class="mt-2 text-gray-500 dark:text-gray-400">Loading templates...</p>
          </div>

          <div v-else-if="templates.length === 0" class="text-center py-4">
            <p class="text-gray-500 dark:text-gray-400">
              No templates available. Please import templates from Amiqus.
            </p>
          </div>

          <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div
              v-for="template in templates"
              :key="template.id"
              class="relative rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-4 shadow-sm hover:shadow-md transition-shadow duration-200 cursor-pointer"
              :class="{
                'border-indigo-500 ring-2 ring-indigo-500':
                  selectedTemplate && selectedTemplate.id === template.id,
              }"
              @click="selectTemplate(template)"
            >
              <div class="flex justify-between">
                <div>
                  <h3 class="text-base font-medium text-gray-900 dark:text-white">
                    {{ template.name }}
                  </h3>
                  <p
                    v-if="template.description"
                    class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                  >
                    {{ template.description }}
                  </p>
                </div>
                <div
                  v-if="selectedTemplate && selectedTemplate.id === template.id"
                  class="text-indigo-600 dark:text-indigo-400"
                >
                  <svg
                    class="h-5 w-5"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Error message -->
      <div
        v-if="error"
        class="mt-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-900/30 rounded-md p-4"
      >
        <div class="flex">
          <div class="flex-shrink-0">
            <svg
              class="h-5 w-5 text-red-400"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 20 20"
              fill="currentColor"
              aria-hidden="true"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-red-700 dark:text-red-400">{{ error }}</p>
          </div>
        </div>
      </div>

      <!-- Navigation buttons -->
      <div class="mt-8 flex justify-between">
        <button
          v-if="currentStep > 1"
          type="button"
          class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          @click="previousStep"
        >
          <svg
            class="-ml-1 mr-2 h-5 w-5"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
              clip-rule="evenodd"
            />
          </svg>
          Back
        </button>
        <div v-else></div>

        <button
          v-if="currentStep < 2"
          type="button"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          :disabled="!canProceedToNextStep"
          :class="{ 'opacity-50 cursor-not-allowed': !canProceedToNextStep }"
          @click="nextStep"
        >
          Next
          <svg
            class="ml-2 -mr-1 h-5 w-5"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
        <button
          v-else
          type="button"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          :disabled="!canSubmit || isSubmitting"
          :class="{ 'opacity-50 cursor-not-allowed': !canSubmit || isSubmitting }"
          @click="submitWizard"
        >
          <span v-if="isSubmitting">
            <svg
              class="animate-spin -ml-1 mr-2 h-5 w-5 text-white"
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
            Processing...
          </span>
          <span v-else>Create Background Check</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

// State
const currentStep = ref(1);
const candidateSearch = ref('');
const candidates = ref([]);
const selectedCandidate = ref(null);
const templates = ref([]);
const selectedTemplate = ref(null);
const error = ref('');
const isLoadingCandidates = ref(false);
const isLoadingTemplates = ref(false);
const isSubmitting = ref(false);
let searchTimeout = null;

// Computed properties
const canProceedToNextStep = computed(() => {
  if (currentStep.value === 1) {
    return !!selectedCandidate.value;
  }
  return false;
});

const canSubmit = computed(() => {
  return !!selectedCandidate.value && !!selectedTemplate.value;
});

// Methods
const nextStep = () => {
  if (currentStep.value < 2 && canProceedToNextStep.value) {
    currentStep.value++;
    if (currentStep.value === 2) {
      loadTemplates();
    }
  }
};

const previousStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--;
  }
};

const searchCandidates = async () => {
  if (!candidateSearch.value.trim()) {
    candidates.value = [];
    return;
  }

  isLoadingCandidates.value = true;
  error.value = '';

  try {
    const response = await axios.get('/api/ats/check-wizard/candidates', {
      params: { query: candidateSearch.value },
    });

    candidates.value = response.data.data?.candidates || [];
  } catch (err) {
    console.error('Error searching candidates:', err);
    error.value = 'Failed to search candidates. Please try again.';
  } finally {
    isLoadingCandidates.value = false;
  }
};

const debounceSearchCandidates = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    searchCandidates();
  }, 300);
};

const selectCandidate = candidate => {
  selectedCandidate.value = candidate;
  candidateSearch.value = '';
  candidates.value = [];
};

const clearSelectedCandidate = () => {
  selectedCandidate.value = null;
};

const loadTemplates = async () => {
  isLoadingTemplates.value = true;
  error.value = '';

  try {
    const response = await axios.get('/api/ats/check-wizard/templates');
    templates.value = response.data.data?.templates || [];
  } catch (err) {
    console.error('Error loading templates:', err);
    error.value = 'Failed to load templates. Please try again.';
  } finally {
    isLoadingTemplates.value = false;
  }
};

const selectTemplate = template => {
  selectedTemplate.value = template;
};

const submitWizard = async () => {
  if (!canSubmit.value || isSubmitting.value) {
    return;
  }

  isSubmitting.value = true;
  error.value = '';

  try {
    const response = await axios.post('/api/ats/check-wizard/process', {
      candidate_id: selectedCandidate.value.id,
      template_id: selectedTemplate.value.amiqus_id,
    });

    if (response.data.success) {
      // Redirect to the candidate profile with background check tab highlighted
      router.push(`/candidates/${response.data.candidate_id}#background-checks`);
    } else {
      error.value = response.data.message || 'Failed to create background check.';
    }
  } catch (err) {
    console.error('Error submitting wizard:', err);
    error.value =
      err.response?.data?.message || 'Failed to create background check. Please try again.';
  } finally {
    isSubmitting.value = false;
  }
};
</script>
