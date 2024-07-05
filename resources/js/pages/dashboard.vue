<script lang="ts" setup>
import LayoutMain from "@/layouts/main/layoutMain.vue";
import { Head } from "@inertiajs/vue3";
</script>

<template>
  <Head title="Dashboard" />

  <LayoutMain>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">Dashboard</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">You're logged in!</div>
        </div>
      </div>
    </div>
  </LayoutMain>
</template>
