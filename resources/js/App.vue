<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <header class="bg-white dark:bg-gray-800 shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <router-link to="/" class="flex items-center">
                <img
                  src="./assets/amiqus-logo.svg"
                  alt="Amiqus Logo"
                  class="h-8 mr-2 block dark:hidden"
                />
                <img
                  src="./assets/amiqus-logo-white.svg"
                  alt="Amiqus Logo"
                  class="h-8 mr-2 hidden dark:block"
                />
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">ATS Demo</h1>
              </router-link>
            </div>
            <nav class="ml-6 flex space-x-8">
              <router-link
                to="/"
                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                :class="[
                  $route.name === 'home'
                    ? 'border-indigo-500 text-gray-900 dark:text-white'
                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-300',
                ]"
              >
                Home
              </router-link>
              <router-link
                to="/candidates"
                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                :class="[
                  $route.name === 'candidates'
                    ? 'border-indigo-500 text-gray-900 dark:text-white'
                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-300',
                ]"
              >
                Candidates
              </router-link>
              <router-link
                to="/background-checks"
                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                :class="[
                  $route.name === 'background-checks'
                    ? 'border-indigo-500 text-gray-900 dark:text-white'
                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-300',
                ]"
              >
                Background Checks
              </router-link>
              <router-link
                to="/integrations/settings"
                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                :class="[
                  $route.name === 'integrations.settings'
                    ? 'border-indigo-500 text-gray-900 dark:text-white'
                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-300',
                ]"
              >
                Integration Settings
              </router-link>
            </nav>
          </div>
          <div class="flex items-center">
            <SearchBar />
            <DarkModeToggle />
          </div>
        </div>
      </div>
    </header>
    <Breadcrumbs v-if="$route.name !== 'home'" />
    <main>
      <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
          <router-view />
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import Breadcrumbs from './components/Breadcrumbs.vue';
import DarkModeToggle from './components/DarkModeToggle.vue';
import SearchBar from './components/SearchBar.vue';

const $route = useRoute();
</script>
