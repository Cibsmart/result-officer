<script lang="ts" setup>
import { Link } from "@inertiajs/vue3";
import { PlusIcon } from "@heroicons/vue/20/solid";

defineProps<{
  href: string;
}>();
</script>

<template>
  <Link
    :href="href"
    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
    <PlusIcon
      aria-hidden="true"
      class="-ml-0.5 mr-1.5 h-5 w-5" />

    <slot />
  </Link>
</template>
