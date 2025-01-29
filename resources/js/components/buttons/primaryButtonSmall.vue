<script lang="ts" setup></script>
<template>
  <button
    class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-2 py-1 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden active:bg-gray-900 disabled:cursor-not-allowed disabled:bg-gray-400 disabled:hover:bg-gray-400 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white dark:focus:bg-white dark:focus:ring-offset-gray-800 dark:active:bg-gray-300"
    type="submit">
    <slot />
  </button>
</template>
