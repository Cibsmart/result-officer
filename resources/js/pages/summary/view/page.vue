<script lang="ts" setup>
import { BreadcrumbItem } from "@/types";
import { Head } from "@inertiajs/vue3";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseLink from "@/components/links/baseLink.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";
import { computed } from "vue";
import Students from "@/pages/summary/view/partials/students.vue";
import EmptyState from "@/components/emptyState.vue";
import IconLink from "@/components/links/iconLink.vue";

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
      <div>
        <div class="sm:flex sm:items-center">
          <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Department Results Summary</h1>

            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
              List of {{ department.level.name }} level {{ department.department.name }} students in
              {{ department.session.name }} with their current CGPA sorted from highest to lowest
            </p>
          </div>

          <div class="mt-4 flex space-x-4">
            <BaseLink
              :href="
                route('summary.print', {
                  department: department.department.id,
                  session: department.session.id,
                  level: department.level.id,
                })
              ">
              Print
            </BaseLink>
          </div>
        </div>

        <div
          class="mt-5 divide-y divide-solid divide-gray-300 ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:divide-gray-600 dark:ring-gray-600">
          <div class="p-2">
            DEPARTMENT: <span class="font-bold text-black dark:text-white">{{ department.department.name }}</span>
          </div>

          <div class="p-2">
            SESSION:
            <span class="font-bold text-black dark:text-white">{{ department.session.name }}</span>
          </div>

          <div class="p-2">
            LEVEL: <span class="font-bold text-black dark:text-white">{{ department.level.name }}</span>
          </div>
        </div>

        <Students
          v-if="hasSummary"
          :students="department.students" />

        <EmptyState
          v-else
          description="Get started by downloading students from the Portal"
          title="No Summary">
          <IconLink :href="route('download.students.page')">Download Students</IconLink>
        </EmptyState>
      </div>
    </BaseSection>
  </BasePage>
</template>

<style scoped></style>
