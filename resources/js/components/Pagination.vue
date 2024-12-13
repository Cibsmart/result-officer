<script lang="ts" setup>
import { Link } from "@inertiajs/vue3";
import { PaginationLink } from "@/types/paginate";

defineProps<{
  links?: Array<PaginationLink>;
}>();
</script>

<template>
  <div class="-mb-1 mt-6 flex flex-wrap">
    <template v-for="(link, key) in links">
      <template v-if="link.url === null">
        <div
          :key="key + 'empty'"
          class="mb-1 mr-1 rounded border px-4 py-3 text-sm">
          {{ link.label }}
        </div>
      </template>

      <template v-else>
        <Link
          :key="key + 'non-empty'"
          :class="{ 'bg-white text-gray-900 dark:bg-gray-700': link.active }"
          :href="link.url"
          class="mb-1 mr-1 rounded border border-gray-400 px-4 py-3 text-sm hover:bg-white dark:text-gray-300 dark:hover:bg-gray-700"
          preserve-scroll
          preserve-state>
          {{ link.label }}
        </Link>
      </template>
    </template>
  </div>
</template>
