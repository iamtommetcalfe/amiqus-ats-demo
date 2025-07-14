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
        <!-- Success and Error Messages -->
        <div
          v-if="
            amiqusClientCreationSuccess ||
            amiqusClientUpdateSuccess ||
            amiqusClientCreationError ||
            amiqusClientUpdateError
          "
          class="mb-4"
        >
          <!-- Create Success message -->
          <div
            v-if="amiqusClientCreationSuccess && !amiqusClientUpdateSuccess"
            class="p-2 bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 rounded flex justify-between items-center"
          >
            <span>Person created successfully in Amiqus!</span>
            <button
              class="text-green-800 dark:text-green-100 hover:text-green-600 dark:hover:text-green-300 focus:outline-none"
              @click="dismissCreationSuccess"
            >
              <svg
                class="h-5 w-5"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>

          <!-- Update Success message -->
          <div
            v-if="amiqusClientUpdateSuccess"
            class="p-2 bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 rounded flex justify-between items-center"
          >
            <span>Person updated successfully in Amiqus!</span>
            <button
              class="text-green-800 dark:text-green-100 hover:text-green-600 dark:hover:text-green-300 focus:outline-none"
              @click="dismissUpdateSuccess"
            >
              <svg
                class="h-5 w-5"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>

          <!-- Error message -->
          <div
            v-if="amiqusClientCreationError || amiqusClientUpdateError"
            class="p-2 bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100 rounded flex justify-between items-center"
          >
            <span>{{ amiqusClientCreationError || amiqusClientUpdateError }}</span>
            <button
              class="text-red-800 dark:text-red-100 hover:text-red-600 dark:hover:text-red-300 focus:outline-none"
              @click="dismissErrors"
            >
              <svg
                class="h-5 w-5"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
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

        <!-- Candidate Header -->
        <div class="border-b border-gray-200 dark:border-gray-700 pb-5">
          <div class="flex justify-between items-start">
            <div>
              <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
                {{ candidate.first_name }} {{ candidate.last_name }}
              </h2>
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
                      d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"
                    />
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                  </svg>
                  {{ candidate.email }}
                </div>
                <div
                  v-if="candidate.phone"
                  class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-300"
                >
                  <svg
                    class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400 dark:text-gray-500"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                  >
                    <path
                      d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"
                    />
                  </svg>
                  {{ candidate.phone }}
                </div>
              </div>
            </div>

            <!-- Amiqus Buttons, Background Check Status, and Application Count -->
            <div class="flex items-center">
              <!-- Background Check Status Indicator -->
              <span
                v-if="amiqus.is_connected && backgroundChecks.length > 0"
                class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium mr-2"
                :class="getBackgroundCheckStatusClass()"
              >
                <svg
                  class="mr-1 h-4 w-4"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                {{ getBackgroundCheckStatusText() }}
              </span>

              <span
                v-if="applications.length > 0"
                class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 mr-4"
              >
                {{ applications.length }}
                {{ applications.length === 1 ? 'Application' : 'Applications' }}
              </span>

              <!-- Create Person in Amiqus button -->
              <div v-if="!amiqus.is_connected" class="relative group">
                <button
                  :disabled="isCreatingAmiqusClient || !hasActiveIntegration"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                  :class="{
                    'cursor-pointer': hasActiveIntegration,
                    'cursor-not-allowed': !hasActiveIntegration,
                  }"
                  @click="createAmiqusClient"
                >
                  <svg
                    v-if="isCreatingAmiqusClient"
                    class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
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
                  {{ isCreatingAmiqusClient ? 'Creating...' : 'Create Person in Amiqus' }}
                </button>
                <!-- Tooltip that appears on hover when button is disabled due to no active integration -->
                <div
                  v-if="!hasActiveIntegration"
                  class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-64 px-3 py-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none"
                >
                  <div class="flex items-center">
                    <svg
                      class="h-4 w-4 mr-1 text-yellow-400"
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <span
                      >Please set up the Amiqus integration in the
                      <a href="/integrations/settings" class="underline hover:text-blue-300"
                        >integration settings</a
                      >
                      page first.</span
                    >
                  </div>
                  <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1">
                    <svg
                      class="h-2 w-2 text-gray-800"
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path d="M10 20l-5-5 5-5 5 5z" />
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Amiqus Dropdown Button -->
              <div v-else id="amiqus-dropdown-container" class="relative">
                <button
                  id="amiqus-dropdown-button"
                  type="button"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 cursor-pointer"
                  @click="toggleAmiqusDropdown"
                >
                  Amiqus
                  <svg
                    class="ml-2 -mr-0.5 h-4 w-4"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>

                <!-- Dropdown Menu -->
                <div
                  v-if="showAmiqusDropdown"
                  id="amiqus-dropdown-menu"
                  class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                >
                  <div class="py-1">
                    <!-- Create Person option - only shown when not connected -->
                    <div v-if="!amiqus.is_connected" class="relative group">
                      <a
                        :class="[
                          'block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600',
                          hasActiveIntegration ? 'cursor-pointer' : 'cursor-not-allowed opacity-50',
                        ]"
                        @click="hasActiveIntegration && createAmiqusClient"
                      >
                        <div class="flex items-center">
                          <svg
                            v-if="isCreatingAmiqusClient"
                            class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-700 dark:text-gray-200"
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
                          {{ isCreatingAmiqusClient ? 'Creating...' : 'Create Person' }}
                        </div>
                      </a>
                      <!-- Tooltip for dropdown menu item -->
                      <div
                        v-if="!hasActiveIntegration"
                        class="absolute left-full top-0 ml-2 w-64 px-3 py-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none"
                      >
                        <div class="flex items-center">
                          <svg
                            class="h-4 w-4 mr-1 text-yellow-400"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                              clip-rule="evenodd"
                            />
                          </svg>
                          <span
                            >Please set up the Amiqus integration in the integration settings page
                            first.</span
                          >
                        </div>
                        <div class="absolute right-full top-1/2 transform -translate-y-1/2">
                          <svg
                            class="h-2 w-2 text-gray-800"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path d="M0 10l5-5 5 5-5 5z" />
                          </svg>
                        </div>
                      </div>
                    </div>
                    <a
                      v-if="amiqus.is_connected"
                      class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer"
                      @click="updateAmiqusClient"
                    >
                      <div class="flex items-center">
                        <svg
                          v-if="isUpdatingAmiqusClient"
                          class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-700 dark:text-gray-200"
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
                        {{ isUpdatingAmiqusClient ? 'Updating...' : 'Update Person' }}
                      </div>
                    </a>
                    <a
                      v-if="amiqus.is_connected"
                      class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer"
                      @click="openBackgroundCheckModal"
                    >
                      <div class="flex items-center">
                        <svg
                          v-if="isOpeningBackgroundCheckModal"
                          class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-700 dark:text-gray-200"
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
                        {{ isOpeningBackgroundCheckModal ? 'Loading...' : 'Send Background Check' }}
                      </div>
                    </a>
                    <a
                      v-if="amiqus.is_connected"
                      :href="amiqus.client_url"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer"
                    >
                      View in Amiqus
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Current Position -->
          <div v-if="candidate.current_position || candidate.current_company" class="mt-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Current Position</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">
              {{ candidate.current_position || 'Not specified' }} at
              {{ candidate.current_company || 'Not specified' }}
            </p>
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

        <!-- Tabbed Section -->
        <div class="mt-6">
          <!-- Tab Navigation -->
          <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex -mb-px space-x-8" aria-label="Tabs">
              <a
                v-for="tab in tabs"
                :key="tab.id"
                :href="`#${tab.id}`"
                :class="[
                  activeTab === tab.id
                    ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-gray-600',
                  'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                ]"
                @click.prevent="setActiveTab(tab.id)"
              >
                {{ tab.name }}
                <span
                  v-if="tab.count !== undefined"
                  :class="[
                    activeTab === tab.id
                      ? 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-400'
                      : 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-gray-300',
                    'ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium',
                  ]"
                >
                  {{ tab.count }}
                </span>
              </a>
            </nav>
          </div>

          <!-- Tab Content -->
          <div class="mt-4">
            <!-- Job Applications Tab -->
            <div v-if="activeTab === 'applications'">
              <!-- Success message -->
              <div
                v-if="backgroundCheckSyncSuccess"
                class="mt-2 p-2 bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 rounded flex justify-between items-center"
              >
                <span>{{ backgroundCheckSyncSuccess }}</span>
                <button
                  class="text-green-800 dark:text-green-100 hover:text-green-600 dark:hover:text-green-300 focus:outline-none"
                  @click="backgroundCheckSyncSuccess = null"
                >
                  <svg
                    class="h-5 w-5"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </div>

              <div
                v-if="applications.length === 0"
                class="mt-2 text-sm text-gray-500 dark:text-gray-400"
              >
                No job applications found for this candidate.
              </div>

              <div v-else class="mt-4 space-y-6">
                <div
                  v-for="(application, index) in applications"
                  :key="index"
                  class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg"
                >
                  <div class="flex justify-between items-start">
                    <div>
                      <router-link
                        :to="`/jobs/${application.job_posting.id}`"
                        class="text-md font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                      >
                        {{ application.job_posting.title }}
                      </router-link>
                      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ application.job_posting.department }} ·
                        {{ application.job_posting.location }}
                      </p>
                      <div class="mt-2 flex items-center">
                        <div
                          class="w-3 h-3 rounded-full mr-2"
                          :style="{ backgroundColor: application.interview_stage.color }"
                        ></div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                          {{ application.interview_stage.name }}
                        </span>
                      </div>
                      <p
                        v-if="application.scheduled_at"
                        class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                      >
                        <span class="font-medium">Scheduled:</span>
                        {{ formatDateTime(application.scheduled_at) }}
                      </p>
                      <p
                        v-if="application.notes"
                        class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                      >
                        <span class="font-medium">Notes:</span> {{ application.notes }}
                      </p>
                      <p
                        v-if="application.feedback"
                        class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                      >
                        <span class="font-medium">Feedback:</span> {{ application.feedback }}
                      </p>
                    </div>
                    <span
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="getStatusClass(application.status)"
                    >
                      {{ application.status }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Background Checks Tab -->
            <div v-if="activeTab === 'background-checks' && amiqus.is_connected">
              <!-- Success message -->
              <div
                v-if="backgroundCheckSyncSuccess"
                class="mt-2 p-2 bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 rounded flex justify-between items-center"
              >
                <span>{{ backgroundCheckSyncSuccess }}</span>
                <button
                  class="text-green-800 dark:text-green-100 hover:text-green-600 dark:hover:text-green-300 focus:outline-none"
                  @click="backgroundCheckSyncSuccess = null"
                >
                  <svg
                    class="h-5 w-5"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </div>

              <!-- Error message -->
              <div
                v-if="backgroundCheckSyncError"
                class="mt-2 p-2 bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100 rounded flex justify-between items-center"
              >
                <span>{{ backgroundCheckSyncError }}</span>
                <button
                  class="text-red-800 dark:text-red-100 hover:text-red-600 dark:hover:text-red-300 focus:outline-none"
                  @click="backgroundCheckSyncError = null"
                >
                  <svg
                    class="h-5 w-5"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </div>

              <div
                v-if="loadingBackgroundChecks"
                class="mt-2 text-sm text-gray-500 dark:text-gray-400"
              >
                Loading background checks...
              </div>

              <div
                v-else-if="backgroundChecks.length === 0"
                class="mt-2 text-sm text-gray-500 dark:text-gray-400"
              >
                No background checks found for this candidate.
              </div>

              <div v-else class="mt-4 space-y-4">
                <div
                  v-for="check in backgroundChecks"
                  :key="check.id"
                  class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg"
                >
                  <div class="flex justify-between items-start">
                    <div>
                      <h4 class="text-md font-medium text-gray-900 dark:text-white">
                        {{ check.template_name }}
                      </h4>
                      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Cost: {{ Math.round(check.cost) }}
                        {{ Math.round(check.cost) === 1 ? 'credit' : 'credits' }}
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
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="getStatusClass(check.status)"
                      >
                        {{ check.status }}
                      </span>
                      <div class="flex space-x-2">
                        <button
                          :disabled="syncingBackgroundChecks[check.id]"
                          class="inline-flex items-center text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 disabled:opacity-50 disabled:cursor-not-allowed"
                          @click="syncBackgroundCheck(check.id)"
                        >
                          <svg
                            v-if="syncingBackgroundChecks[check.id]"
                            class="animate-spin -ml-1 mr-1 h-4 w-4"
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
                          <svg
                            v-else
                            class="mr-1 h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                            />
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
                          <svg
                            class="ml-1 h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"
                            />
                            <path
                              d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"
                            />
                          </svg>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- API Logs Tab -->
            <div v-if="activeTab === 'api-logs'">
              <div v-if="loadingApiLogs" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Loading API logs...
              </div>

              <div v-else-if="apiLogsError" class="mt-2 text-sm text-red-500">
                {{ apiLogsError }}
              </div>

              <div
                v-else-if="apiLogs.length === 0"
                class="mt-2 text-sm text-gray-500 dark:text-gray-400"
              >
                No API logs found for this candidate.
              </div>

              <div v-else class="mt-4 space-y-4">
                <div
                  v-for="log in apiLogs"
                  :key="log.id"
                  class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg"
                >
                  <div class="flex justify-between items-start">
                    <div>
                      <h4 class="text-md font-medium text-gray-900 dark:text-white">
                        {{ log.method }} {{ log.url.split('/').slice(-2).join('/') }}
                      </h4>
                      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Status:
                        <span
                          :class="log.response_status >= 400 ? 'text-red-500' : 'text-green-500'"
                          >{{ log.response_status || 'Error' }}</span
                        >
                      </p>
                      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Time: {{ formatDateTime(log.created_at) }}
                      </p>
                      <p v-if="log.duration" class="text-sm text-gray-500 dark:text-gray-400">
                        Duration: {{ Math.round(log.duration * 1000) }}ms
                      </p>
                      <p v-if="log.error" class="text-sm text-red-500">Error: {{ log.error }}</p>

                      <!-- Collapsible details -->
                      <div class="mt-2">
                        <button
                          class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 flex items-center"
                          @click="log.showDetails = !log.showDetails"
                        >
                          <svg
                            class="h-4 w-4 mr-1"
                            :class="{ 'transform rotate-90': log.showDetails }"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                              clip-rule="evenodd"
                            />
                          </svg>
                          {{ log.showDetails ? 'Hide Details' : 'Show Details' }}
                        </button>

                        <div v-if="log.showDetails" class="mt-2 space-y-2">
                          <div
                            v-if="log.request_headers"
                            class="bg-gray-100 dark:bg-gray-800 p-2 rounded"
                          >
                            <h5 class="text-sm font-medium text-gray-900 dark:text-white">
                              Request Headers
                            </h5>
                            <pre
                              class="text-xs text-gray-700 dark:text-gray-300 overflow-auto max-h-40"
                              >{{ JSON.stringify(log.request_headers, null, 2) }}</pre
                            >
                          </div>

                          <div
                            v-if="log.request_body"
                            class="bg-gray-100 dark:bg-gray-800 p-2 rounded"
                          >
                            <h5 class="text-sm font-medium text-gray-900 dark:text-white">
                              Request Body
                            </h5>
                            <pre
                              class="text-xs text-gray-700 dark:text-gray-300 overflow-auto max-h-40"
                              >{{ JSON.stringify(log.request_body, null, 2) }}</pre
                            >
                          </div>

                          <div
                            v-if="log.response_headers"
                            class="bg-gray-100 dark:bg-gray-800 p-2 rounded"
                          >
                            <h5 class="text-sm font-medium text-gray-900 dark:text-white">
                              Response Headers
                            </h5>
                            <pre
                              class="text-xs text-gray-700 dark:text-gray-300 overflow-auto max-h-40"
                              >{{ JSON.stringify(log.response_headers, null, 2) }}</pre
                            >
                          </div>

                          <div
                            v-if="log.response_body"
                            class="bg-gray-100 dark:bg-gray-800 p-2 rounded"
                          >
                            <h5 class="text-sm font-medium text-gray-900 dark:text-white">
                              Response Body
                            </h5>
                            <pre
                              class="text-xs text-gray-700 dark:text-gray-300 overflow-auto max-h-40"
                              >{{ JSON.stringify(log.response_body, null, 2) }}</pre
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div
    v-if="showBackgroundCheckModal"
    class="fixed inset-0 z-10 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
  >
    <div
      class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center relative z-10"
    >
      <!-- Background overlay -->
      <div
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        aria-hidden="true"
        @click="closeBackgroundCheckModal"
      ></div>

      <!-- Modal panel -->
      <div
        class="inline-block align-middle bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-20 w-full max-w-md mx-auto"
        style="transform: translate(0, 0)"
      >
        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <h3
                id="modal-title"
                class="text-lg leading-6 font-medium text-gray-900 dark:text-white"
              >
                Send Background Check
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                  Select a request template to send a background check to this candidate.
                </p>
              </div>

              <!-- Loading state -->
              <div v-if="loadingTemplates" class="mt-4 flex justify-center">
                <svg
                  class="animate-spin h-5 w-5 text-indigo-600"
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
                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400"
                  >Loading templates...</span
                >
              </div>

              <!-- Error state -->
              <div v-else-if="templatesError" class="mt-4 text-sm text-red-500">
                {{ templatesError }}
              </div>

              <!-- Empty state -->
              <div
                v-else-if="templates.length === 0"
                class="mt-4 text-sm text-gray-500 dark:text-gray-400"
              >
                No request templates available. Please import templates from Amiqus first.
              </div>

              <!-- Template list -->
              <div v-else class="mt-4">
                <div class="space-y-2">
                  <div
                    v-for="template in templates"
                    :key="template.amiqus_id"
                    class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-md cursor-pointer"
                    :class="{
                      'bg-indigo-50 dark:bg-indigo-900 border-indigo-300 dark:border-indigo-700':
                        selectedTemplateId === template.amiqus_id,
                    }"
                    @click="selectTemplate(template.amiqus_id)"
                  >
                    <div class="flex-1">
                      <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ template.name }}
                      </h4>
                      <p
                        v-if="template.description"
                        class="text-xs text-gray-500 dark:text-gray-400"
                      >
                        {{ template.description }}
                      </p>
                    </div>
                    <div
                      v-if="selectedTemplateId === template.amiqus_id"
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
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            type="button"
            :disabled="!selectedTemplateId || isSubmittingBackgroundCheck"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            @click="submitBackgroundCheck"
          >
            <svg
              v-if="isSubmittingBackgroundCheck"
              class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
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
            {{ isSubmittingBackgroundCheck ? 'Sending...' : 'Send' }}
          </button>
          <button
            type="button"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            @click="closeBackgroundCheckModal"
          >
            Cancel
          </button>
          <button
            type="button"
            :disabled="loadingTemplates"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            @click="refreshTemplates"
          >
            <svg
              v-if="loadingTemplates"
              class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-700 dark:text-gray-300"
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
            Refresh
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, defineProps } from 'vue';
import axios from 'axios';

const props = defineProps({
  id: {
    type: String,
    required: true,
  },
});

const candidate = ref({});
const applications = ref([]);
const loading = ref(true);
const error = ref(null);
const amiqus = ref({ is_connected: false, client_url: null });
const isCreatingAmiqusClient = ref(false);
const isUpdatingAmiqusClient = ref(false);
const amiqusClientCreationError = ref(null);
const amiqusClientCreationSuccess = ref(false);
const amiqusClientUpdateError = ref(null);
const amiqusClientUpdateSuccess = ref(false);
const showAmiqusDropdown = ref(false);
const hasActiveIntegration = ref(false);

// Tab state
const tabs = ref([
  { id: 'applications', name: 'Job Applications', count: 0 },
  { id: 'background-checks', name: 'Background Checks', count: 0 },
  { id: 'api-logs', name: 'API Logs', count: 0 },
]);
const activeTab = ref('applications');

// Store reference to the hash change handler for proper cleanup
const handleHashChange = () => {
  if (window.location.hash) {
    const hash = window.location.hash.substring(1);
    if (['applications', 'background-checks', 'api-logs'].includes(hash)) {
      activeTab.value = hash;
    }
  } else {
    // Default to applications tab if no hash
    activeTab.value = 'applications';
  }
};

// Background checks state
const backgroundChecks = ref([]);
const loadingBackgroundChecks = ref(false);
const backgroundCheckError = ref(null);
const syncingBackgroundChecks = ref({});
const backgroundCheckSyncSuccess = ref(null);
const backgroundCheckSyncError = ref(null);

// API logs state
const apiLogs = ref([]);
const loadingApiLogs = ref(false);
const apiLogsError = ref(null);

// Request templates state
const templates = ref([]);
const loadingTemplates = ref(false);
const templatesError = ref(null);

// Background check modal state
const showBackgroundCheckModal = ref(false);
const selectedTemplateId = ref(null);
const isSubmittingBackgroundCheck = ref(false);
const isOpeningBackgroundCheckModal = ref(false);
const backgroundCheckSubmissionError = ref(null);
const backgroundCheckSubmissionSuccess = ref(false);

/**
 * Set the active tab and update the URL hash.
 */
const setActiveTab = tabId => {
  activeTab.value = tabId;
  // Update the URL hash without triggering a page reload
  window.history.pushState(null, null, `#${tabId}`);
};

/**
 * Update the tab counts based on the loaded data.
 */
const updateTabCounts = () => {
  // Find the applications tab and update its count
  const applicationsTab = tabs.value.find(tab => tab.id === 'applications');
  if (applicationsTab) {
    applicationsTab.count = applications.value.length;
  }

  // Find the background checks tab and update its count
  const backgroundChecksTab = tabs.value.find(tab => tab.id === 'background-checks');
  if (backgroundChecksTab) {
    backgroundChecksTab.count = backgroundChecks.value.length;
  }

  // Find the API logs tab and update its count
  const apiLogsTab = tabs.value.find(tab => tab.id === 'api-logs');
  if (apiLogsTab) {
    apiLogsTab.count = apiLogs.value.length;
  }
};

onMounted(async () => {
  try {
    if (!props.id) {
      error.value = 'Candidate ID is missing. Please go back to the job listings.';
      loading.value = false;
      return;
    }

    // Check if there's a hash in the URL and set the active tab accordingly
    handleHashChange();

    // Fetch integration status
    try {
      const integrationResponse = await axios.get('/api/amiqus/settings');
      hasActiveIntegration.value = integrationResponse.data.isConnected;
    } catch (integrationErr) {
      console.error('Error fetching integration status:', integrationErr);
      hasActiveIntegration.value = false;
    }

    const response = await axios.get(`/api/ats/candidates/${props.id}`);

    // Check if response.data and response.data.data exist
    if (response.data && response.data.data) {
      candidate.value = response.data.data.candidate || {};
      applications.value = response.data.data.applications || [];

      // Update the applications tab count
      updateTabCounts();

      // Set Amiqus client information
      if (response.data.data.amiqus) {
        amiqus.value = response.data.data.amiqus;

        // If candidate is connected to Amiqus, fetch background checks
        if (amiqus.value.is_connected) {
          fetchBackgroundChecks();
        }

        // Fetch API logs for debugging
        fetchApiLogs();
      }
    } else {
      console.warn('Unexpected API response structure:', response.data);
      error.value = 'Failed to load candidate details. Unexpected API response structure.';
    }
  } catch (err) {
    error.value = 'Failed to load candidate details. Please try again later.';
    console.error(err);
  } finally {
    loading.value = false;
  }

  // Add event listener to close dropdown when clicking outside
  document.addEventListener('click', closeAmiqusDropdown);

  // Add event listener for hash changes to support browser back/forward navigation
  window.addEventListener('hashchange', handleHashChange);
});

onUnmounted(() => {
  // Remove event listeners to prevent memory leaks
  document.removeEventListener('click', closeAmiqusDropdown);
  window.removeEventListener('hashchange', handleHashChange);
});

/**
 * Fetch background checks for the candidate.
 */
const fetchBackgroundChecks = async () => {
  loadingBackgroundChecks.value = true;
  backgroundCheckError.value = null;

  try {
    const response = await axios.get(`/api/ats/candidates/${props.id}/background-checks`);
    backgroundChecks.value = response.data.background_checks;

    // Update the tab counts after loading the data
    updateTabCounts();
  } catch (err) {
    console.error('Error fetching background checks:', err);
    backgroundCheckError.value = 'Failed to load background checks. Please try again.';
  } finally {
    loadingBackgroundChecks.value = false;
  }
};

/**
 * Fetch API logs for the candidate.
 */
const fetchApiLogs = async () => {
  loadingApiLogs.value = true;
  apiLogsError.value = null;

  try {
    const response = await axios.get(`/api/ats/candidates/${props.id}/api-logs`);
    if (response.data.success && response.data.data && response.data.data.api_logs) {
      // Initialize showDetails property for each log
      apiLogs.value = response.data.data.api_logs.map(log => ({
        ...log,
        showDetails: false,
      }));

      // Update the tab counts after loading the data
      updateTabCounts();
    } else {
      console.warn('Unexpected API response structure:', response.data);
      apiLogsError.value = 'Failed to load API logs. Unexpected response structure.';
    }
  } catch (err) {
    console.error('Error fetching API logs:', err);
    apiLogsError.value = 'Failed to load API logs. Please try again.';
  } finally {
    loadingApiLogs.value = false;
  }
};

/**
 * Open the background check modal and fetch templates.
 */
const openBackgroundCheckModal = async () => {
  isOpeningBackgroundCheckModal.value = true;

  try {
    // Keep dropdown open to show loading state
    showBackgroundCheckModal.value = true;
    selectedTemplateId.value = null;
    backgroundCheckSubmissionError.value = null;
    backgroundCheckSubmissionSuccess.value = false;
    await fetchTemplates();
  } catch (err) {
    console.error('Error opening background check modal:', err);
  } finally {
    isOpeningBackgroundCheckModal.value = false;
  }
};

/**
 * Close the background check modal.
 */
const closeBackgroundCheckModal = () => {
  showBackgroundCheckModal.value = false;
};

/**
 * Fetch request templates from the API.
 */
const fetchTemplates = async () => {
  loadingTemplates.value = true;
  templatesError.value = null;

  try {
    const response = await axios.get('/api/amiqus/templates');
    templates.value = response.data.templates;
    return response.data.templates;
  } catch (err) {
    console.error('Error fetching templates:', err);
    templatesError.value = 'Failed to load templates. Please try again.';
    throw err;
  } finally {
    loadingTemplates.value = false;
  }
};

/**
 * Refresh the templates list.
 */
const refreshTemplates = () => {
  fetchTemplates();
};

/**
 * Select a template for the background check.
 */
const selectTemplate = templateId => {
  selectedTemplateId.value = templateId;
};

/**
 * Submit a background check request.
 */
const submitBackgroundCheck = async () => {
  if (!selectedTemplateId.value) {
    return;
  }

  isSubmittingBackgroundCheck.value = true;
  backgroundCheckSubmissionError.value = null;
  backgroundCheckSubmissionSuccess.value = false;

  try {
    const response = await axios.post(`/api/ats/candidates/${props.id}/background-checks`, {
      template_id: selectedTemplateId.value,
    });

    if (response.data.success) {
      backgroundCheckSubmissionSuccess.value = true;

      // Add the new background check to the list
      backgroundChecks.value.unshift(response.data.background_check);

      // Update the tab counts
      updateTabCounts();

      // Switch to the background checks tab
      setActiveTab('background-checks');

      // Close the modal after a short delay
      setTimeout(() => {
        closeBackgroundCheckModal();
      }, 1500);
    } else {
      backgroundCheckSubmissionError.value =
        response.data.message || 'Failed to create background check.';
    }
  } catch (err) {
    console.error('Error creating background check:', err);
    backgroundCheckSubmissionError.value =
      err.response?.data?.message || 'Failed to create background check. Please try again.';
  } finally {
    isSubmittingBackgroundCheck.value = false;
  }
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

/**
 * Get the overall background check status class based on all background checks.
 * Priority: In Progress > Completed > Cancelled
 */
const getBackgroundCheckStatusClass = () => {
  if (!backgroundChecks.value || backgroundChecks.value.length === 0) {
    return 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100';
  }

  // Check if any background checks are pending
  if (backgroundChecks.value.some(check => check.status === 'pending')) {
    return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100';
  }

  // Check if any background checks are scheduled
  if (backgroundChecks.value.some(check => check.status === 'scheduled')) {
    return 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100';
  }

  // Check if any background checks are completed
  if (backgroundChecks.value.some(check => check.status === 'completed')) {
    return 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100';
  }

  // If all background checks are cancelled or have an unknown status
  return 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100';
};

/**
 * Get the overall background check status text based on all background checks.
 */
const getBackgroundCheckStatusText = () => {
  if (!backgroundChecks.value || backgroundChecks.value.length === 0) {
    return 'No Checks';
  }

  // Count the number of checks in each status
  const statusCounts = backgroundChecks.value.reduce((counts, check) => {
    counts[check.status] = (counts[check.status] || 0) + 1;
    return counts;
  }, {});

  // Determine the overall status text
  if (statusCounts['pending'] > 0) {
    return `${statusCounts['pending']} Check${statusCounts['pending'] > 1 ? 's' : ''} Pending`;
  } else if (statusCounts['scheduled'] > 0) {
    return `${statusCounts['scheduled']} Check${statusCounts['scheduled'] > 1 ? 's' : ''} In Progress`;
  } else if (statusCounts['completed'] > 0) {
    return `${statusCounts['completed']} Check${statusCounts['completed'] > 1 ? 's' : ''} Completed`;
  } else {
    return `${backgroundChecks.value.length} Check${backgroundChecks.value.length > 1 ? 's' : ''} Cancelled`;
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
  // Keep dropdown open to show loading state

  try {
    const response = await axios.post(`/api/ats/candidates/${props.id}/amiqus-client`, {
      title: 'mr', // Default title, could be made configurable
    });

    if (response.data.success) {
      // Update local state
      amiqus.value.is_connected = true;

      // Check if response.data.data exists and contains the expected fields
      if (response.data.data && response.data.data.amiqus_client_url) {
        amiqus.value.client_url = response.data.data.amiqus_client_url;
      } else {
        console.warn('Unexpected API response structure:', response.data);
      }

      amiqusClientCreationSuccess.value = true;

      // Update candidate data
      if (response.data.data && response.data.data.candidate) {
        candidate.value = response.data.data.candidate;
      } else {
        console.warn('Unexpected API response structure:', response.data);
      }
    } else {
      amiqusClientCreationError.value = response.data.message || 'Failed to create Amiqus client.';
    }
  } catch (err) {
    console.error('Error creating Amiqus client:', err);
    amiqusClientCreationError.value =
      err.response?.data?.message || 'Failed to create Amiqus client. Please try again.';
  } finally {
    isCreatingAmiqusClient.value = false;
    // Close dropdown after operation completes
    showAmiqusDropdown.value = false;
  }
};

/**
 * Update a client in Amiqus.
 */
const updateAmiqusClient = async () => {
  // Reset state
  amiqusClientUpdateError.value = null;
  amiqusClientUpdateSuccess.value = false;
  isUpdatingAmiqusClient.value = true;
  // Keep dropdown open to show loading state

  try {
    const response = await axios.patch(`/api/ats/candidates/${props.id}/amiqus-client`, {
      title: 'mr', // Default title, could be made configurable
    });

    if (response.data.success) {
      // Update local state
      amiqusClientUpdateSuccess.value = true;
      amiqusClientUpdateError.value = null;

      // Reset creation messages
      amiqusClientCreationSuccess.value = false;
      amiqusClientCreationError.value = null;

      // Update candidate data
      if (response.data.data && response.data.data.candidate) {
        candidate.value = response.data.data.candidate;
      } else {
        console.warn('Unexpected API response structure:', response.data);
      }
    } else {
      amiqusClientUpdateError.value = response.data.message || 'Failed to update Amiqus client.';
      amiqusClientUpdateSuccess.value = false;

      // Reset creation messages
      amiqusClientCreationSuccess.value = false;
      amiqusClientCreationError.value = null;
    }
  } catch (err) {
    console.error('Error updating Amiqus client:', err);
    amiqusClientUpdateError.value =
      err.response?.data?.message || 'Failed to update Amiqus client. Please try again.';
    amiqusClientUpdateSuccess.value = false;

    // Reset creation messages
    amiqusClientCreationError.value = null;
    amiqusClientCreationSuccess.value = false;
  } finally {
    isUpdatingAmiqusClient.value = false;
    // Close dropdown after operation completes
    showAmiqusDropdown.value = false;
  }
};

/**
 * Toggle the Amiqus dropdown menu.
 */
const toggleAmiqusDropdown = () => {
  showAmiqusDropdown.value = !showAmiqusDropdown.value;
};

/**
 * Close the Amiqus dropdown menu when clicking outside.
 */
const closeAmiqusDropdown = event => {
  if (showAmiqusDropdown.value) {
    const dropdown = document.getElementById('amiqus-dropdown-container');
    if (dropdown && !dropdown.contains(event.target)) {
      showAmiqusDropdown.value = false;
    }
  }
};

/**
 * Dismiss creation success message.
 */
const dismissCreationSuccess = () => {
  amiqusClientCreationSuccess.value = false;
};

/**
 * Dismiss update success message.
 */
const dismissUpdateSuccess = () => {
  amiqusClientUpdateSuccess.value = false;
};

/**
 * Sync a background check with the Amiqus API.
 *
 * @param {number} backgroundCheckId - The ID of the background check to sync
 */
const syncBackgroundCheck = async backgroundCheckId => {
  // Set syncing state for this specific background check
  syncingBackgroundChecks.value[backgroundCheckId] = true;

  // Reset success/error messages
  backgroundCheckSyncSuccess.value = null;
  backgroundCheckSyncError.value = null;

  try {
    const response = await axios.post(
      `/api/ats/candidates/${props.id}/background-checks/${backgroundCheckId}/sync`
    );

    if (response.data.success) {
      // Update the background check in the list
      const index = backgroundChecks.value.findIndex(check => check.id === backgroundCheckId);
      if (index !== -1) {
        backgroundChecks.value[index] = response.data.background_check;
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
    backgroundCheckSyncError.value =
      err.response?.data?.message || 'Failed to sync background check. Please try again.';
  } finally {
    // Clear syncing state
    syncingBackgroundChecks.value[backgroundCheckId] = false;
  }
};

/**
 * Dismiss error messages.
 */
const dismissErrors = () => {
  amiqusClientCreationError.value = null;
  amiqusClientUpdateError.value = null;
  backgroundCheckSyncError.value = null;
};
</script>
