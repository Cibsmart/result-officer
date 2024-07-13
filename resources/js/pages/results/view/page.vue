<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BaseSection from "@/components/baseSection.vue";
import { BreadcrumbItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";
import ButtonIndigo from "@/components/buttons/buttonIndigo.vue";
import Session from "@/pages/results/view/partials/session.vue";
import EmptyState from "@/components/emptyState.vue";
import { computed } from "vue";

const props = defineProps<{
  student: App.Data.Students.StudentData;
  results: App.Data.Results.StudentResultData;
}>();

const pages: BreadcrumbItem[] = [
  { name: "Result Form", href: route("results.form"), current: route().current("results.form") },
  { name: "Result View", href: route("results.view"), current: route().current("results.view") },
];

const hasResults = computed(() => props.results.enrollments.length > 0);
const studentName = computed(() => `${props.student.lastName} ${props.student.firstName} ${props.student.otherNames}`);
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

          <div
            v-show="hasResults"
            class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <ButtonIndigo>Print</ButtonIndigo>
          </div>
        </div>

        <div
          class="mt-5 divide-y divide-solid divide-gray-300 ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:divide-gray-600 dark:ring-gray-600">
          <div class="p-2">
            NAME: <span class="font-bold text-black dark:text-white">{{ studentName }}</span>
          </div>

          <div class="p-2">
            REGISTRATION NUMBER:
            <span class="font-bold text-black dark:text-white">{{ student.matriculationNumber }}</span>
          </div>

          <div class="p-2">
            DEPARTMENT: <span class="font-bold text-black dark:text-white">{{ student.department }}</span>
          </div>
        </div>

        <div>
          <template v-if="hasResults">
            <Session
              v-for="session in results.enrollments"
              :key="session.id"
              :session="session" />
          </template>

          <template v-else>
            <EmptyState />
          </template>
        </div>

        <div
          v-if="hasResults"
          class="mt-2 flex flex-col p-2 text-center text-xl font-bold uppercase text-black lg:block dark:text-white">
          <span>
            Current Final CGPA:
            <span>{{ results.finalCumulativeGradePointAverage }} </span>
          </span>

          <span class="hidden lg:inline"> (</span>

          <span class=""> {{ results.classOfDegree }}</span>

          <span class="hidden lg:inline">)</span>
        </div>
      </div>
    </BaseSection>
  </BasePage>
</template>
