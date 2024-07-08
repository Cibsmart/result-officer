<script lang="ts" setup>
import { HomeIcon } from "@heroicons/vue/20/solid";
import { Link } from "@inertiajs/vue3";
import { BreadcrumbItem } from "@/types";

defineProps<{
  pages?: BreadcrumbItem[];
}>();
</script>

<template>
  <nav
    aria-label="Breadcrumb"
    class="flex border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
    <ol
      class="mx-auto flex w-full max-w-screen-xl space-x-2 px-4 sm:px-6 lg:px-8"
      role="list">
      <li class="flex">
        <div class="flex items-center">
          <Link
            :href="route('dashboard')"
            class="py-3 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
            <HomeIcon
              aria-hidden="true"
              class="h-5 w-5 flex-shrink-0" />
            <span class="sr-only">Home</span>
          </Link>
        </div>
      </li>
      <li
        v-for="page in pages"
        :key="page.name"
        class="flex">
        <div class="flex items-center">
          <svg
            aria-hidden="true"
            class="h-full w-6 flex-shrink-0 text-gray-200 dark:text-gray-700"
            fill="currentColor"
            preserveAspectRatio="none"
            viewBox="0 0 24 44">
            <path d="M.293 0l22 22-22 22h1.414l22-22-22-22H.293z" />
          </svg>
          <Link
            :aria-current="page.current ? 'page' : undefined"
            :href="page.href"
            class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-50">
            {{ page.name }}
          </Link>
        </div>
      </li>
    </ol>
  </nav>
</template>
