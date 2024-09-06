<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import { BreadcrumbItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseLink from "@/components/links/baseLink.vue";
import { computed } from "vue";
import EmptyState from "@/components/emptyState.vue";
import IconLink from "@/components/links/iconLink.vue";

const props = defineProps<{
  data: App.Data.Composite.CompositeSheetData;
}>();

const pages: BreadcrumbItem[] = [
  { name: "Composite Sheet Form", href: route("composite.form"), current: route().current("composite.form") },
  { name: "Composite Sheet View", href: route("composite.view"), current: route().current("composite.view") },
];

const hasRows = computed(() => props.data.students.length > 0);
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
              This page shows Composite Sheet for {{ data.program.name }}
            </p>
          </div>

          <BaseLink
            v-show="hasRows"
            :href="
              route('composite.print', {
                program: data.program.id,
                session: data.session.id,
                level: data.level.id,
                semester: data.semester.id,
              })
            ">
            Download
          </BaseLink>
        </div>

        <div
          class="mt-5 divide-y divide-solid divide-gray-300 ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:divide-gray-600 dark:ring-gray-600">
          <div class="grid grid-flow-col">
            <div class="p-2">
              FACULTY: <span class="font-bold text-black dark:text-white">{{ data.faculty.name }}</span>
            </div>

            <div class="p-2">
              DEPARTMENT: <span class="font-bold text-black dark:text-white">{{ data.program.name }}</span>
            </div>
          </div>

          <div class="grid grid-flow-col">
            <div class="p-2">
              SESSION: <span class="font-bold text-black dark:text-white"> {{ data.session.name }} </span>
            </div>

            <div class="p-2">
              SEMESTER: <span class="font-bold text-black dark:text-white"> {{ data.semester.name }} </span>
            </div>

            <div class="p-2">
              LEVEL: <span class="font-bold text-black dark:text-white"> {{ data.level.name }} </span>
            </div>
          </div>
        </div>

        <div>
          <template v-if="hasRows">
            <div class="mt-8 flow-root">
              <div class="overflow-auto">
                <div
                  class="inline-block max-h-[600px] min-w-full divide-y divide-solid divide-gray-300 ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:divide-gray-600 dark:ring-gray-600">
                  <table class="divide min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead>
                      <tr class="divide-x divide-gray-200 dark:divide-gray-600">
                        <th />

                        <th
                          class="sticky top-0 z-10 bg-opacity-75 p-1 text-center text-xs font-semibold text-gray-900 backdrop-blur dark:text-white"
                          colspan="2"
                          scope="col">
                          COURSE CODE
                        </th>

                        <th
                          v-for="(course, index) in data.courses"
                          :key="index"
                          class="sticky top-0 z-10 bg-opacity-75 p-1 text-center text-xs font-semibold text-gray-900 backdrop-blur dark:text-white"
                          colspan="2"
                          scope="col">
                          {{ course.unit }}
                        </th>

                        <th
                          v-if="data.hasOtherCourses"
                          class="sticky top-0 z-10 whitespace-nowrap bg-opacity-75 p-1 text-center align-bottom text-xs font-semibold text-gray-900 backdrop-blur dark:text-white"
                          scope="col">
                          OTHER
                        </th>

                        <th
                          class="sticky top-0 z-10 bg-opacity-75 p-1 text-center text-xs font-semibold text-gray-900 backdrop-blur dark:text-white"
                          colspan="3"
                          scope="col">
                          TOTALS
                        </th>

                        <th />
                      </tr>

                      <tr class="divide-x divide-gray-200 dark:divide-gray-600">
                        <th
                          class="sticky top-0 z-10 bg-opacity-75 p-1 text-center text-xs font-semibold text-gray-900 backdrop-blur dark:text-white"
                          scope="col">
                          SN
                        </th>

                        <th
                          class="sticky top-0 z-10 border-t bg-opacity-75 p-1 text-center text-xs font-semibold backdrop-blur"
                          scope="col">
                          NAME
                        </th>

                        <th
                          class="sticky top-0 z-10 whitespace-nowrap border-t bg-opacity-75 p-1 text-center text-xs font-semibold"
                          scope="col">
                          REGISTRATION NUMBER
                        </th>

                        <th
                          v-for="(course, index) in data.courses"
                          :key="index"
                          class="sticky top-0 z-10 border-t p-1 text-center text-xs font-semibold text-gray-900 backdrop-blur dark:text-white"
                          colspan="2"
                          scope="col">
                          {{ course.code }}
                        </th>

                        <th
                          v-if="data.hasOtherCourses"
                          class="sticky top-0 z-10 whitespace-nowrap bg-opacity-75 p-1 text-center text-xs font-semibold text-gray-900 backdrop-blur dark:text-white"
                          scope="col">
                          COURSES
                        </th>

                        <th
                          class="sticky top-0 z-10 border-t bg-opacity-75 p-1 text-center text-xs font-semibold text-gray-900 backdrop-blur dark:text-white"
                          scope="col">
                          TCL
                        </th>

                        <th
                          class="sticky top-0 z-10 border-t bg-opacity-75 p-1 text-center text-xs font-semibold text-gray-900 backdrop-blur dark:text-white"
                          scope="col">
                          TGP
                        </th>

                        <th
                          class="sticky top-0 z-10 border-t bg-opacity-75 p-1 text-center text-xs font-semibold text-gray-900 backdrop-blur dark:text-white"
                          scope="col">
                          GPA
                        </th>

                        <th
                          class="sticky top-0 z-10 bg-opacity-75 p-1 text-center text-xs font-semibold text-gray-900 backdrop-blur dark:text-white"
                          scope="col">
                          REMARKS
                        </th>
                      </tr>
                    </thead>

                    <tbody>
                      <tr
                        v-for="(student, index) in data.students"
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
                          v-if="data.hasOtherCourses"
                          class="border-t border-gray-200 p-1 text-center text-xs text-gray-700 dark:border-gray-700 dark:text-gray-300">
                          {{ student.otherCourses }}
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
                          class="border-t border-gray-200 p-1 text-left text-xs text-gray-700 dark:border-gray-700 dark:text-gray-300">
                          {{ student.remark }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </template>

          <EmptyState
            v-else
            description="Get started by downloading students from the Portal"
            title="No Student Found">
            <IconLink :href="route('download.students.page')">Download Students</IconLink>
          </EmptyState>
        </div>
      </div>
    </BaseSection>
  </BasePage>
</template>
