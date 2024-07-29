<script lang="ts" setup>
import { BreadcrumbItem } from "@/types";
import { Head } from "@inertiajs/vue3";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseLink from "@/components/links/baseLink.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BaseSection from "@/components/baseSection.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";

defineProps<{
  department: App.Data.Summary.DepartmentResultSummaryData;
}>();

const pages: BreadcrumbItem[] = [
  { name: "Summary Form", href: route("summary.form"), current: route().current("summary.form") },
  { name: "Summary View", href: route("summary.view"), current: route().current("summary.view") },
];
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

        <div class="-mx-4 mt-2 overflow-auto ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:ring-gray-600">
          <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
            <thead>
              <tr>
                <th
                  class="hidden px-3 py-2 text-center text-sm font-semibold text-gray-900 lg:table-cell dark:text-white"
                  scope="col">
                  SN
                </th>

                <th
                  class="py-2 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 dark:text-white"
                  scope="col">
                  STUDENTS' NAME
                </th>

                <th
                  class="hidden px-3 py-2 text-left text-sm font-semibold text-gray-900 lg:table-cell dark:text-white"
                  scope="col">
                  REGISTRATION NUMBER
                </th>

                <th
                  class="px-3 py-2 text-center text-sm font-semibold text-gray-900 dark:text-white"
                  scope="col">
                  CGPA
                </th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="(student, index) in department.students"
                :key="student.student.id">
                <td
                  class="hidden border-t border-gray-200 px-3 py-2 text-center text-sm text-gray-700 lg:table-cell dark:border-gray-700 dark:text-gray-300">
                  {{ index + 1 }}
                </td>

                <td class="relative border-t border-gray-200 py-2 pl-4 pr-3 text-sm sm:pl-6 dark:border-gray-700">
                  {{ student.student.name }}
                </td>

                <td class="relative border-t border-gray-200 py-2 pl-4 pr-3 text-sm sm:pl-6 dark:border-gray-700">
                  {{ student.student.matriculationNumber }}
                </td>

                <td
                  class="border-t border-gray-200 px-3 py-2 text-center text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">
                  {{ student.fcgpa }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </BaseSection>
  </BasePage>
</template>

<style scoped></style>
