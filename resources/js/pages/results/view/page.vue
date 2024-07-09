<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BaseSection from "@/components/baseSection.vue";
import { BreadcrumbItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";
import ButtonIndigo from "@/components/buttons/buttonIndigo.vue";

defineProps<{
  student: App.Data.Students.StudentData;
  results?: App.Data.Results.StudentResultData;
}>();

const pages: BreadcrumbItem[] = [
  { name: "Result Form", href: route("results.form"), current: route().current("results.form") },
  { name: "Result View", href: route("results.view"), current: route().current("results.view") },
];

const plans = [
  {
    id: 1,
    name: "CSC 101",
    memory: "INTRODUCTION TO COMPUTER SCIENCE",
    storage: 3,
    price: "A",
    isCurrent: false,
  },
  {
    id: 2,
    name: "CSC 102",
    memory: "INTRODUCTION TO PROGRAMMING",
    storage: 3,
    price: "F",
    isCurrent: true,
  },
  // More plans...
];
</script>

<template>
  <Head title="View Student Result" />

  <Breadcrumb :pages="pages" />

  <BaseHeader> View Student Results</BaseHeader>

  <BasePage>
    <BaseSection>
      <div class="px-4 sm:px-6 lg:px-8 dark:bg-gray-900">
        <div class="sm:flex sm:items-center">
          <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Student Results</h1>

            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
              This page is shows all results for the selected student
            </p>
          </div>

          <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <ButtonIndigo> Print</ButtonIndigo>
          </div>
        </div>

        <div class="-mx-4 mt-10 ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:ring-gray-600">
          <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
            <thead>
              <tr>
                <th
                  class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 dark:text-white"
                  scope="col">
                  Course Code
                </th>

                <th
                  class="hidden px-3 py-3.5 text-left text-sm font-semibold text-gray-900 lg:table-cell dark:text-white"
                  scope="col">
                  Course Title
                </th>

                <th
                  class="hidden px-3 py-3.5 text-left text-sm font-semibold text-gray-900 lg:table-cell dark:text-white"
                  scope="col">
                  Credit Unit
                </th>

                <th
                  class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white"
                  scope="col">
                  Grade
                </th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="(plan, planIdx) in plans"
                :key="plan.id">
                <td
                  class="relative py-4 pl-4 pr-3 text-sm sm:pl-6"
                  :class="[planIdx === 0 ? '' : 'border-t border-transparent']">
                  <div class="font-medium text-gray-900 dark:text-white">
                    {{ plan.name }}
                  </div>

                  <div class="mt-1 flex flex-col text-gray-700 sm:block lg:hidden dark:text-gray-300">
                    <span>{{ plan.memory }} </span>

                    <span class="hidden sm:inline"> - </span>

                    <span>{{ plan.storage }}</span>
                  </div>

                  <div
                    v-if="planIdx !== 0"
                    class="absolute -top-px left-6 right-0 h-px bg-gray-200 dark:bg-gray-700" />
                </td>

                <td
                  class="hidden px-3 py-3.5 text-sm text-gray-700 lg:table-cell dark:text-gray-300"
                  :class="[planIdx === 0 ? '' : 'border-t border-gray-200 dark:border-gray-700']">
                  {{ plan.memory }}
                </td>

                <td
                  class="hidden px-3 py-3.5 text-sm text-gray-700 lg:table-cell dark:text-gray-300"
                  :class="[planIdx === 0 ? '' : 'border-t border-gray-200 dark:border-gray-700']">
                  {{ plan.storage }}
                </td>

                <td
                  class="px-3 py-3.5 text-sm text-gray-700 dark:text-gray-300"
                  :class="[planIdx === 0 ? '' : 'border-t border-gray-200 dark:border-gray-700']">
                  <div class="sm:hidden">{{ plan.price }}</div>

                  <div class="hidden sm:block">{{ plan.price }}</div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </BaseSection>
  </BasePage>
</template>
