<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import { BreadcrumbItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";
import { computed } from "vue";

const props = defineProps<{
  students: Array<App.Data.Composite.CompositeRowData>;
  courses: Array<App.Data.Composite.CompositeCourseListData>;
}>();

const pages: BreadcrumbItem[] = [
  { name: "Composite Sheet Form", href: route("composite.form"), current: route().current("composite.form") },
  { name: "Composite Sheet View", href: route("composite.view"), current: route().current("composite.view") },
];

const formatOtherCourses = (otherCourses: Array<App.Data.Composite.CompositeCourseData>) => {
  return otherCourses.reduce((acc, curr) => `${acc} ${curr.code} (${curr.grade}),`, "");
};

const hasOthers = computed(() => props.students.some((student) => student.otherCourses.length > 0));
</script>

<template>
  <Head title="Composite Sheet" />

  <Breadcrumb :pages="pages" />

  <BaseHeader> Composite Sheet</BaseHeader>

  <BasePage>
    <BaseSection>
      <div class="rounded px-4 py-4 sm:px-6 lg:px-8 dark:bg-gray-900">
        <div class="sm:flex sm:items-center">
          <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Composite Sheet</h1>

            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
              This page shows Composite Sheet for the selected department
            </p>
          </div>
        </div>

        <div
          class="mt-5 divide-y divide-solid divide-gray-300 ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:divide-gray-600 dark:ring-gray-600">
          <div class="p-2">NAME: <span class="font-bold text-black dark:text-white">IFEBUDE BARNABAS</span></div>

          <div class="p-2">
            REGISTRATION NUMBER:
            <span class="font-bold text-black dark:text-white">EBSU/2009/51486</span>
          </div>

          <div class="p-2">DEPARTMENT: <span class="font-bold text-black dark:text-white">COMPUTER SCIENCE</span></div>
        </div>

        <div class="mt-8 flow-root">
          <div class="overflow-x-auto">
            <div
              class="inline-block min-w-full divide-y divide-solid divide-gray-300 ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:divide-gray-600 dark:ring-gray-600">
              <table class="divide min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead>
                  <tr class="divide-x divide-gray-200 dark:divide-gray-600">
                    <th
                      class="p-1 text-center text-xs font-semibold text-gray-900 dark:text-white"
                      rowspan="2"
                      scope="col">
                      SN
                    </th>

                    <th
                      class="p-1 text-center text-xs font-semibold text-gray-900 dark:text-white"
                      colspan="2"
                      scope="col">
                      COURSE CODE
                    </th>

                    <th
                      v-for="(course, index) in courses"
                      :key="index"
                      class="p-1 text-center text-xs font-semibold text-gray-900 dark:text-white"
                      colspan="2"
                      scope="col">
                      {{ course.unit }}
                    </th>

                    <th />

                    <th
                      class="p-1 text-center text-xs font-semibold text-gray-900 dark:text-white"
                      colspan="4"
                      scope="col">
                      CURRENT TOTALS
                    </th>

                    <th />
                  </tr>

                  <tr class="divide-x divide-gray-200 dark:divide-gray-600">
                    <th class="p-1 text-center text-xs font-semibold">NAME</th>

                    <th class="whitespace-nowrap p-1 text-center text-xs font-semibold">REGISTRATION NUMBER</th>

                    <th
                      v-for="(course, index) in courses"
                      :key="index"
                      class="p-1 text-center text-xs font-semibold text-gray-900 dark:text-white"
                      colspan="2"
                      scope="col">
                      {{ course.code }}
                    </th>

                    <th
                      v-if="hasOthers"
                      class="whitespace-nowrap p-1 text-center text-xs font-semibold text-gray-900 dark:text-white"
                      scope="col">
                      Other Courses
                    </th>

                    <th
                      class="p-1 text-center text-xs font-semibold text-gray-900 dark:text-white"
                      scope="col">
                      TCL
                    </th>

                    <th
                      class="p-1 text-center text-xs font-semibold text-gray-900 dark:text-white"
                      scope="col">
                      GP
                    </th>

                    <th
                      class="p-1 text-center text-xs font-semibold text-gray-900 dark:text-white"
                      scope="col">
                      GPA
                    </th>

                    <th
                      class="p-1 text-center text-xs font-semibold text-gray-900 dark:text-white"
                      scope="col">
                      REMARK
                    </th>
                  </tr>
                </thead>

                <tbody>
                  <tr
                    v-for="(student, index) in students"
                    :key="index"
                    class="divide-x divide-gray-200 dark:divide-gray-600">
                    <td
                      class="border-t border-gray-200 p-1 text-center text-xs text-gray-700 dark:border-gray-700 dark:text-gray-300">
                      {{ index + 1 }}
                    </td>

                    <td class="whitespace-nowrap border-t border-gray-200 p-1 text-xs dark:border-gray-700">
                      {{ student.studentName }}
                    </td>

                    <td
                      class="whitespace-nowrap border-t border-gray-200 p-1 text-center text-xs text-gray-700 dark:border-gray-700 dark:text-gray-300">
                      {{ student.registrationNumber }}
                    </td>

                    <template
                      v-for="(course, index) in student.levelCourses"
                      :key="index">
                      <td
                        class="min-w-8 border-t border-gray-200 p-1 text-center text-xs text-gray-700 dark:border-gray-700 dark:text-gray-300">
                        {{ course.score }}
                      </td>

                      <td
                        class="min-w-8 border-t border-gray-200 p-1 text-center text-xs text-gray-700 dark:border-gray-700 dark:text-gray-300">
                        {{ course.grade }}
                      </td>
                    </template>

                    <td
                      v-if="hasOthers"
                      class="border-t border-gray-200 p-1 text-center text-xs text-gray-700 dark:border-gray-700 dark:text-gray-300">
                      {{ formatOtherCourses(student.otherCourses) }}
                    </td>

                    <td
                      class="border-t border-gray-200 p-1 text-center text-xs text-gray-700 dark:border-gray-700 dark:text-gray-300">
                      {{ student.creditUnitTotal }}
                    </td>

                    <td
                      class="border-t border-gray-200 p-1 text-center text-xs text-gray-700 dark:border-gray-700 dark:text-gray-300">
                      {{ student.gradePointTotal }}
                    </td>

                    <td
                      class="border-t border-gray-200 p-1 text-center text-xs text-gray-700 dark:border-gray-700 dark:text-gray-300">
                      {{ student.gradePointAverage }}
                    </td>

                    <td
                      class="border-t border-gray-200 p-1 text-center text-xs text-gray-700 dark:border-gray-700 dark:text-gray-300">
                      PASS
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </BaseSection>
  </BasePage>
</template>
