<template>
  <button
    class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none"
    :title="isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'"
    @click="toggleDarkMode"
  >
    <!-- Sun icon for dark mode (shown when in dark mode) -->
    <svg
      v-if="isDarkMode"
      xmlns="http://www.w3.org/2000/svg"
      class="h-5 w-5"
      viewBox="0 0 20 20"
      fill="currentColor"
    >
      <path
        fill-rule="evenodd"
        d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
        clip-rule="evenodd"
      />
    </svg>
    <!-- Moon icon for light mode (shown when in light mode) -->
    <svg
      v-else
      xmlns="http://www.w3.org/2000/svg"
      class="h-5 w-5"
      viewBox="0 0 20 20"
      fill="currentColor"
    >
      <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
    </svg>
  </button>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';

// State for dark mode
const isDarkMode = ref(false);

// Function to toggle dark mode
const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value;
  updateDarkMode();
};

// Function to update the DOM based on dark mode state
const updateDarkMode = () => {
  // Update localStorage
  localStorage.setItem('darkMode', isDarkMode.value ? 'dark' : 'light');

  // Update the HTML element class and style
  if (isDarkMode.value) {
    document.documentElement.classList.add('dark');
    document.documentElement.style.colorScheme = 'dark';
  } else {
    document.documentElement.classList.remove('dark');
    document.documentElement.style.colorScheme = 'light';
  }

  // Force a repaint to ensure styles are applied
  document.body.style.display = 'none';
  document.body.offsetHeight; // Trigger a reflow
  document.body.style.display = '';
};

// Initialize dark mode on component mount
onMounted(() => {
  // Check if the HTML element already has the 'dark' class
  const hasDarkClass = document.documentElement.classList.contains('dark');

  // Set initial state based on the HTML element's class
  isDarkMode.value = hasDarkClass;

  // No need to call updateDarkMode() here since we're just syncing with the existing state

  // Listen for system preference changes
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
    // Only update if user hasn't set a preference
    if (!localStorage.getItem('darkMode')) {
      isDarkMode.value = e.matches;
      updateDarkMode();
    }
  });
});

// Watch for changes to isDarkMode and update accordingly
watch(isDarkMode, () => {
  updateDarkMode();
});
</script>
