<script lang="ts" setup>
import { PlusIcon } from "@heroicons/vue/20/solid";
</script>

<template>
  <div class="-mx-4 mt-5 p-3 ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:ring-gray-600">
    <div class="text-center">
      <svg
        aria-hidden="true"
        class="mx-auto h-12 w-12 text-gray-400"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24">
        <path
          d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          vector-effect="non-scaling-stroke" />
      </svg>

      <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No Result</h3>

      <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">
        Get started by downloading student's results from the Portal
      </p>

      <div class="mt-6">
        <button
          class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          type="button">
          <PlusIcon
            aria-hidden="true"
            class="-ml-0.5 mr-1.5 h-5 w-5" />
          Download Student Results
        </button>
      </div>
    </div>
  </div>
</template>
