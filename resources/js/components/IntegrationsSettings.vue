<template>
  <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <!-- Modal for displaying API test results -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">API Test Results</h3>
          <button @click="showModal = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <div class="px-6 py-4 overflow-auto max-h-[70vh]">
          <pre v-if="testResult" class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg overflow-auto text-sm text-gray-800 dark:text-white">{{ JSON.stringify(testResult, null, 2) }}</pre>
          <div v-else-if="isLoading" class="flex justify-center items-center py-10">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"></div>
          </div>
          <p v-else class="text-gray-500 dark:text-gray-400">No data available.</p>
        </div>
        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right">
          <button @click="showModal = false" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Close
          </button>
        </div>
      </div>
    </div>

    <div class="p-6 text-gray-900 dark:text-gray-100">
      <h2 class="text-2xl font-semibold mb-4">Amiqus Integration Settings</h2>

      <!-- Loading indicator -->
      <div v-if="isLoadingSettings" class="flex justify-center items-center py-10">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"></div>
      </div>

      <!-- Content (only shown after loading) -->
      <div v-else>
        <!-- Notification message -->
        <div v-if="notification.show" :class="[
          'mb-6 p-4 rounded-lg',
          notification.type === 'success' ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900'
        ]">
          <p :class="[
            notification.type === 'success' ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200'
          ]">
            <span class="font-bold">{{ notification.title }}</span> {{ notification.message }}
          </p>
        </div>

        <div v-if="isConnected" class="mb-6 p-4 bg-green-100 dark:bg-green-900 rounded-lg">
          <p class="text-green-800 dark:text-green-200">
            <span class="font-bold">Connected to Amiqus!</span> Your integration is active.
          </p>
        </div>

        <!-- View mode - show integration details -->
        <div v-if="hasExistingCredentials && !isEditMode" class="mb-6">
          <div class="bg-white dark:bg-gray-700 shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
              <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                  Integration Details
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                  Your Amiqus API integration configuration.
                </p>
              </div>
              <div class="space-x-3">
                <button
                  @click="startEdit"
                  class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Edit
                </button>
                <button
                  @click="deleteIntegration"
                  class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500"
                >
                  Delete
                </button>
              </div>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-600">
              <dl>
                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Integration Name
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                    {{ credentials.name }}
                  </dd>
                </div>
                <div class="bg-white dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Client ID
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                    {{ credentials.client_id }}
                  </dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Redirect URI
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                    {{ credentials.redirect_uri }}
                  </dd>
                </div>
                <div v-if="credentials.scope" class="bg-white dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Scope
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                    {{ credentials.scope }}
                  </dd>
                </div>
              </dl>
            </div>
          </div>
        </div>

        <!-- Edit/Add mode - show form -->
        <form v-if="!hasExistingCredentials || isEditMode" @submit.prevent="saveCredentials" class="space-y-4 mb-6">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Integration Name</label>
            <input
              type="text"
              id="name"
              v-model="formData.name"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 py-2 px-3 h-10"
              placeholder="My Amiqus Integration"
              required
            >
          </div>

          <div>
            <label for="client_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client ID</label>
            <input
              type="text"
              id="client_id"
              v-model="formData.client_id"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 py-2 px-3 h-10"
              placeholder="Your Amiqus Client ID"
              required
            >
          </div>

          <div>
            <label for="client_secret" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client Secret</label>
            <input
              type="password"
              id="client_secret"
              v-model="formData.client_secret"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 py-2 px-3 h-10"
              placeholder="Your Amiqus Client Secret"
              required
            >
          </div>

          <div>
            <label for="redirect_uri" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Redirect URI</label>
            <input
              type="url"
              id="redirect_uri"
              v-model="formData.redirect_uri"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 py-2 px-3 h-10"
              placeholder="https://your-app.com/amiqus/callback"
              required
            >
          </div>

          <div>
            <label for="scope" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Scope (Optional)</label>
            <input
              type="text"
              id="scope"
              v-model="formData.scope"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 py-2 px-3 h-10"
              placeholder="e.g. read write"
            >
          </div>

          <div class="flex space-x-3">
            <button
              type="submit"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              {{ isEditMode ? 'Update' : 'Save' }} Credentials
            </button>
            <button
              v-if="isEditMode"
              type="button"
              @click="cancelEdit"
              class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500"
            >
              Cancel
            </button>
          </div>
        </form>

        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
          <h3 class="text-lg font-medium mb-4">Connection Management</h3>

          <div class="space-x-4">
            <button
              v-if="!isConnected"
              @click="connectToAmiqus"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            >
              Connect to Amiqus
            </button>

            <button
              v-if="isConnected"
              @click="refreshToken"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Refresh Token
            </button>

            <button
              v-if="isConnected"
              @click="disconnect"
              class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600"
            >
              Disconnect
            </button>

            <button
              v-if="isConnected"
              @click="testConnection"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
            >
              Test
            </button>
          </div>
        </div>

        <!-- Request Templates Section - Only shown when connected -->
        <div v-if="isConnected" class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Request Templates</h3>
            <div class="flex space-x-2">
              <button
                @click="importTemplates"
                :disabled="isImporting"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg v-if="isImporting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isImporting ? 'Importing...' : 'Import' }}
              </button>
              <a
                href="https://id.amiqus.co/team/workflow/templates/record"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Manage in Amiqus
              </a>
            </div>
          </div>

          <div v-if="isLoadingTemplates" class="py-4 text-center">
            <div class="animate-spin inline-block h-8 w-8 border-t-2 border-b-2 border-indigo-500 rounded-full"></div>
            <p class="mt-2 text-gray-500 dark:text-gray-400">Loading templates...</p>
          </div>

          <div v-else-if="templatesError" class="py-4">
            <p class="text-red-500">{{ templatesError }}</p>
          </div>

          <div v-else-if="templates.length === 0" class="py-4">
            <p class="text-gray-500 dark:text-gray-400">No templates available. Click the Import button to fetch templates from Amiqus.</p>
          </div>

          <div v-else class="bg-white dark:bg-gray-700 shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200 dark:divide-gray-600">
              <li v-for="template in templates" :key="template.amiqus_id" class="px-4 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ template.name }}</h4>
                    <p v-if="template.description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ template.description }}</p>
                  </div>
                  <div class="ml-2 flex-shrink-0 flex">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                          :class="template.is_enabled ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100'">
                      {{ template.is_enabled ? 'Enabled' : 'Disabled' }}
                    </span>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue';
import axios, { AxiosError, AxiosResponse } from 'axios';

// Type definitions
interface Notification {
  show: boolean;
  type: 'success' | 'error';
  title: string;
  message: string;
  timeout: number | null;
}

interface Credentials {
  name: string;
  client_id: string;
  client_secret: string;
  redirect_uri: string;
  scope: string;
}

interface FormData {
  name: string;
  client_id: string;
  client_secret: string;
  redirect_uri: string;
  scope: string;
}

interface ApiResponse {
  client?: Credentials;
  isConnected: boolean;
  message?: string;
  success?: boolean;
  data?: Record<string, any>;
}

interface TestResult {
  error?: string;
  details?: string;
  [key: string]: any;
}

// Reactive state
const isConnected = ref<boolean>(false);
const hasExistingCredentials = ref<boolean>(false);
const isEditMode = ref<boolean>(false);
const showModal = ref<boolean>(false);
const testResult = ref<TestResult | null>(null);
const isLoading = ref<boolean>(false);
const isLoadingSettings = ref<boolean>(true);
const isLoadingTemplates = ref<boolean>(false);
const isImporting = ref<boolean>(false);
const templates = ref<any[]>([]);
const templatesError = ref<string | null>(null);
const notification = ref<Notification>({
  show: false,
  type: 'success',
  title: '',
  message: '',
  timeout: null
});

const credentials = ref<Credentials>({
  name: '',
  client_id: '',
  client_secret: '',
  redirect_uri: window.location.origin + '/amiqus/callback',
  scope: ''
});

// Separate form data for editing
const formData = reactive<FormData>({
  name: '',
  client_id: '',
  client_secret: '',
  redirect_uri: window.location.origin + '/amiqus/callback',
  scope: ''
});

// Show notification message
const showNotification = (type: 'success' | 'error', title: string, message: string, duration: number = 5000): void => {
  // Clear any existing timeout
  if (notification.value.timeout) {
    clearTimeout(notification.value.timeout);
  }

  // Set notification
  notification.value = {
    show: true,
    type,
    title,
    message,
    timeout: null
  };

  // Auto-hide after duration
  notification.value.timeout = window.setTimeout(() => {
    notification.value.show = false;
  }, duration);
};

// Start editing existing credentials
const startEdit = (): void => {
  // Copy credentials to form data
  formData.name = credentials.value.name;
  formData.client_id = credentials.value.client_id;
  formData.client_secret = credentials.value.client_secret;
  formData.redirect_uri = credentials.value.redirect_uri;
  formData.scope = credentials.value.scope || '';

  isEditMode.value = true;
};

// Cancel editing
const cancelEdit = (): void => {
  isEditMode.value = false;
};

// Delete integration
const deleteIntegration = async (): Promise<void> => {
  if (confirm('Are you sure you want to delete this integration?')) {
    try {
      await axios.delete('/api/amiqus/settings');
      credentials.value = {
        name: '',
        client_id: '',
        client_secret: '',
        redirect_uri: window.location.origin + '/amiqus/callback',
        scope: ''
      };
      hasExistingCredentials.value = false;
      isConnected.value = false;
      showNotification('success', 'Success!', 'Integration deleted successfully.');
    } catch (error) {
      console.error('Error deleting integration:', error);
      showNotification('error', 'Error!', 'Failed to delete integration. Please try again.');
    }
  }
};

// Fetch templates from the API
const fetchTemplates = async (): Promise<void> => {
  if (!isConnected.value) return;

  isLoadingTemplates.value = true;
  templatesError.value = null;

  try {
    const response = await axios.get('/api/amiqus/templates');
    templates.value = response.data.templates;
  } catch (error) {
    console.error('Error fetching templates:', error);
    templatesError.value = 'Failed to load templates. Please try again.';
  } finally {
    isLoadingTemplates.value = false;
  }
};

// Import templates from Amiqus API
const importTemplates = async (): Promise<void> => {
  isImporting.value = true;
  templatesError.value = null;

  try {
    const response = await axios.post('/api/amiqus/templates/import');

    if (response.data.success) {
      showNotification(
        'success',
        'Success!',
        `Templates imported successfully. Imported: ${response.data.imported}, Updated: ${response.data.updated}`
      );
      // Refresh the templates list
      await fetchTemplates();
    } else {
      showNotification('error', 'Error!', response.data.message || 'Failed to import templates.');
    }
  } catch (error) {
    console.error('Error importing templates:', error);
    const axiosError = error as AxiosError<any>;
    showNotification(
      'error',
      'Error!',
      axiosError.response?.data?.message || 'Failed to import templates. Please try again.'
    );
  } finally {
    isImporting.value = false;
  }
};

onMounted(async (): Promise<void> => {
  try {
    const response: AxiosResponse<ApiResponse> = await axios.get('/api/amiqus/settings');
    if (response.data.client) {
      credentials.value = response.data.client;
      hasExistingCredentials.value = true;
    }
    isConnected.value = response.data.isConnected;

    // If connected, fetch templates
    if (isConnected.value) {
      await fetchTemplates();
    }
  } catch (error) {
    console.error('Error fetching settings:', error);
    showNotification('error', 'Error!', 'Failed to load integration settings.');
  } finally {
    isLoadingSettings.value = false;
  }
});

const saveCredentials = async (): Promise<void> => {
  try {
    await axios.post('/api/amiqus/settings', formData);
    // Update the credentials with the form data
    credentials.value = { ...formData };
    hasExistingCredentials.value = true;
    isEditMode.value = false;
    showNotification('success', 'Success!', 'Credentials saved successfully.');
  } catch (error) {
    console.error('Error saving credentials:', error);
    showNotification('error', 'Error!', 'Failed to save credentials. Please try again.');
  }
};

const connectToAmiqus = (): void => {
  window.location.href = '/amiqus/authorize';
};

const refreshToken = async (): Promise<void> => {
  try {
    await axios.post('/api/amiqus/refresh-token');
    showNotification('success', 'Success!', 'Token refreshed successfully.');
  } catch (error) {
    console.error('Error refreshing token:', error);
    showNotification('error', 'Error!', 'Failed to refresh token. Please try again.');
  }
};

const disconnect = async (): Promise<void> => {
  try {
    await axios.post('/api/amiqus/disconnect');
    isConnected.value = false;
    showNotification('success', 'Success!', 'Disconnected from Amiqus successfully.');
  } catch (error) {
    console.error('Error disconnecting:', error);
    showNotification('error', 'Error!', 'Failed to disconnect. Please try again.');
  }
};

const testConnection = async (): Promise<void> => {
  try {
    testResult.value = null; // Clear previous results
    isLoading.value = true; // Set loading state to true
    showModal.value = true; // Show modal immediately with loading state

    const response: AxiosResponse<ApiResponse> = await axios.get('/api/amiqus/test-connection');

    if (response.data.success) {
      testResult.value = response.data.data || {};
      showNotification('success', 'Success!', 'Connection test successful.');
    } else {
      testResult.value = { error: response.data.message };
      showNotification('error', 'Error!', 'Connection test failed.');
    }
  } catch (error) {
    console.error('Error testing connection:', error);
    const axiosError = error as AxiosError<any>;
    testResult.value = {
      error: axiosError.response?.data?.message || axiosError.message || 'Unknown error',
      details: axiosError.response?.data?.error || axiosError.toString()
    };
    showNotification('error', 'Error!', 'Failed to test connection. Please try again.');
  } finally {
    isLoading.value = false; // Set loading state to false regardless of success or failure
  }
};
</script>
