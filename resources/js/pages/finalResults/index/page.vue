<script lang="ts" setup>
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import Breadcrumb from "@/components/breadcrumb.vue";
import { BreadcrumbItem } from "@/types";
import { Head } from "@inertiajs/vue3";
import FinalResultForm from "@/pages/finalResults/index/partials/finalResultForm.vue";
import BaseLink from "@/components/links/baseLink.vue";
import EmptyState from "@/components/emptyState.vue";
import IconLink from "@/components/links/iconLink.vue";
import { computed } from "vue";
import FinalSessionResultsView from "@/pages/finalResults/index/partials/finalSessionResultsView.vue";

const props = defineProps<{
  student: App.Data.Students.StudentBasicData;
  results: App.Data.FinalResults.FinalStudentResultData;
}>();

const pages: BreadcrumbItem[] = [
  { name: "Final Results", href: route("finalResults.index"), current: route().current("finalResults.index") },
];
const hasData = computed(() => props.student !== null);
const hasResults = computed(() => props.results !== null && props.results.finalSessionEnrollments.length > 0);
</script>

<template>
  <Head title="Final Results Page" />

  <Breadcrumb :pages="pages" />

  <BaseHeader> View Student's Final Result</BaseHeader>

  <BasePage>
    <BaseSection>
      <FinalResultForm />
    </BaseSection>

    <BaseSection v-if="hasData">
      <div class="">
        <div class="sm:flex sm:items-center">
          <div class="sm:flex-auto">
            <h1 class="text-base leading-6 font-semibold text-gray-900 dark:text-white">Student Results</h1>

            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
              This page shows all results for the selected student
            </p>
          </div>

          <div
            v-show="hasResults"
            class="mt-4 flex space-x-4">
            <BaseLink :href="route('finalResults.transcript', { student: student })"> Transcript</BaseLink>

            <BaseLink :href="route('finalResults.print', { student: student })"> Print</BaseLink>
          </div>
        </div>

        <div
          class="mt-5 divide-y divide-solid divide-gray-300 ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:divide-gray-600 dark:ring-gray-600">
          <div class="p-2">
            NAME: <span class="font-bold text-black dark:text-white">{{ student.name }}</span>
          </div>

          <div class="p-2">
            REGISTRATION NUMBER:
            <span class="font-bold text-black dark:text-white">{{ student.registrationNumber }}</span>
          </div>

          <div class="p-2">
            DEPARTMENT: <span class="font-bold text-black dark:text-white">{{ student.department }}</span>
          </div>
        </div>

        <div>
          <template v-if="hasResults">
            <FinalSessionResultsView
              v-for="session in results.finalSessionEnrollments"
              :key="session.id"
              :session="session" />
          </template>

          <EmptyState
            v-else
            description="Get started by vetting and clearing student"
            title="No Final Results">
            <IconLink :href="route('vetting.index')">Vet Student</IconLink>
          </EmptyState>
        </div>

        <div
          v-if="hasResults"
          class="mt-2 flex flex-col p-2 text-center text-xl font-bold text-black uppercase lg:block dark:text-white">
          <span>
            Current Final CGPA:
            <span>{{ results.formattedFCGPA }} </span>
          </span>

          <span class="hidden lg:inline"> (</span>

          <span class=""> {{ results.degreeClass }}</span>

          <span class="hidden lg:inline">)</span>
        </div>
      </div>
    </BaseSection>
  </BasePage>
</template>
