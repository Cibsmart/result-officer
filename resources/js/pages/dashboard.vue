<script lang="ts" setup>
import LayoutMain from "@/layouts/main/layoutMain.vue";
import { Head } from "@inertiajs/vue3";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";

defineOptions({ layout: LayoutMain });
</script>

<template>
  <Head title="Dashboard" />

  <BaseHeader>Dashboard</BaseHeader>

  <BasePage>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">You're logged in!</div>
        </div>
      </div>
    </div>
  </BasePage>
</template>
