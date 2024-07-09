<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BaseSection from "@/components/baseSection.vue";
import { BreadcrumbItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";
import ButtonIndigo from "@/components/buttons/buttonIndigo.vue";
import Session from "@/pages/results/view/partials/session.vue";

defineProps<{
  student: App.Data.Students.StudentData;
  results?: App.Data.Results.StudentResultData;
}>();

const pages: BreadcrumbItem[] = [
  { name: "Result Form", href: route("results.form"), current: route().current("results.form") },
  { name: "Result View", href: route("results.view"), current: route().current("results.view") },
];
</script>

<template>
  <Head title="View Student Result" />

  <Breadcrumb :pages="pages" />

  <BaseHeader> View Student Results</BaseHeader>

  <BasePage>
    <BaseSection>
      <div class="rounded px-4 py-4 sm:px-6 lg:px-8 dark:bg-gray-900">
        <div class="sm:flex sm:items-center">
          <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Student Results</h1>

            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
              This page shows all results for the selected student
            </p>
          </div>

          <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <ButtonIndigo>Print</ButtonIndigo>
          </div>
        </div>

        <template
          v-for="session in results?.enrollments"
          :key="session.id">
          <Session :session="session" />
        </template>
      </div>
    </BaseSection>
  </BasePage>
</template>
