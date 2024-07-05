<script lang="ts" setup>
import {
  ChartPieIcon,
  Cog6ToothIcon,
  DocumentDuplicateIcon,
  FolderIcon,
  HomeIcon,
  UsersIcon,
} from "@heroicons/vue/24/outline";
import ApplicationLogo from "@/components/applicationLogo.vue";
import Navigation from "@/layouts/main/partials/navigation.vue";
</script>

<template>
  <div
    class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6 pb-4 dark:bg-gray-900 md:dark:ring-1 md:dark:ring-white/10">
    <div class="flex h-16 shrink-0 items-center">
      <ApplicationLogo />
    </div>

    <nav class="flex flex-1 flex-col">
      <ul
        class="flex flex-1 flex-col gap-y-7"
        role="list">
        <li>
          <Navigation />
        </li>

        <!-- Footer          -->
        <li class="mt-auto">
          <a
            class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-700 hover:bg-gray-50 hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
            href="#">
            <Cog6ToothIcon
              aria-hidden="true"
              class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-white" />
            Settings
          </a>
        </li>
      </ul>
    </nav>
  </div>
</template>
