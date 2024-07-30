<script lang="ts" setup>
import { BreadcrumbItem } from "@/types";
import { Head } from "@inertiajs/vue3";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseLink from "@/components/links/baseLink.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BaseSection from "@/components/baseSection.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";
import { computed } from "vue";
import Students from "@/pages/summary/view/partials/students.vue";
import EmptyState from "@/components/emptyState.vue";

const props = defineProps<{
  department: App.Data.Summary.DepartmentResultSummaryData;
}>();

const pages: BreadcrumbItem[] = [
  { name: "Summary Form", href: route("summary.form"), current: route().current("summary.form") },
  { name: "Summary View", href: route("summary.view"), current: route().current("summary.view") },
];

const hasSummary = computed(() => props.department.students.length > 0);
</script>

<template>
  <Head title="View Department Result Summary" />

  <Breadcrumb :pages="pages" />

  <BaseHeader> View Department Results Summary</BaseHeader>

  <BasePage>
    <BaseSection>
      <div class="rounded px-4 py-4 sm:px-6 lg:px-8 dark:bg-gray-900">
        <div class="sm:flex sm:items-center">
          <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Department Results Summary</h1>

            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
              This page shows list of students in the selected department in a particular session and level
            </p>
          </div>

          <div class="mt-4 flex space-x-4">
            <BaseLink href="#"> Print</BaseLink>
          </div>
        </div>

        <Students
          v-if="hasSummary"
          :students="department.students" />

        <EmptyState
          v-else
          description="Get started by downloading student's results from the Portal"
          title="No Summary" />
      </div>
    </BaseSection>
  </BasePage>
</template>

<style scoped></style>
