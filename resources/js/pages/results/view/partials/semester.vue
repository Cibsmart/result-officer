<script lang="ts" setup>
defineProps<{
  semester: App.Data.Results.SemesterResultData;
}>();
</script>

<template>
  <div class="-mx-4 mt-2 overflow-auto ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:ring-gray-600">
    <div class="min-w-full pt-2 text-center text-sm font-extrabold">{{ semester.semester }} SEMESTER</div>

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
            Course Code
          </th>

          <th
            class="hidden px-3 py-2 text-left text-sm font-semibold text-gray-900 lg:table-cell dark:text-white"
            scope="col">
            Course Title
          </th>

          <th
            class="hidden px-3 py-2 text-center text-sm font-semibold text-gray-900 lg:table-cell dark:text-white"
            scope="col">
            Credit Unit
          </th>

          <th
            class="hidden px-3 py-2 text-center text-sm font-semibold text-gray-900 lg:table-cell dark:text-white"
            scope="col">
            Score
          </th>

          <th
            class="px-3 py-2 text-center text-sm font-semibold text-gray-900 dark:text-white"
            scope="col">
            Grade
          </th>

          <th
            class="hidden px-3 py-2 text-center text-sm font-semibold text-gray-900 lg:table-cell dark:text-white"
            scope="col">
            GP
          </th>

          <th
            class="hidden px-3 py-2 text-center text-sm font-semibold text-gray-900 lg:table-cell dark:text-white"
            scope="col">
            GPA
          </th>
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="(result, index) in semester.results"
          :key="result.id">
          <td
            class="hidden border-t border-gray-200 px-3 py-2 text-center text-sm text-gray-700 lg:table-cell dark:border-gray-700 dark:text-gray-300">
            {{ index + 1 }}
          </td>

          <td class="relative border-t border-gray-200 py-2 pl-4 pr-3 text-sm sm:pl-6 dark:border-gray-700">
            <div class="font-medium text-gray-900 dark:text-white">
              {{ result.courseCode }}
            </div>

            <div class="mt-1 flex flex-col text-gray-700 sm:block lg:hidden dark:text-gray-300">
              <span>{{ result.courseTitle }} </span>

              <span class="hidden sm:inline"> || </span>

              <span>
                <span>Unit: {{ result.creditUnit }} |</span>

                <span>| Score: {{ result.totalScore }} </span>
              </span>
            </div>

            <div
              v-if="index !== 0"
              class="absolute -top-px left-6 right-0 h-px bg-gray-200 dark:bg-gray-700" />
          </td>

          <td
            class="hidden border-t border-gray-200 px-3 py-2 text-sm text-gray-700 lg:table-cell dark:border-gray-700 dark:text-gray-300">
            {{ result.courseTitle }}
          </td>

          <td
            class="hidden border-t border-gray-200 px-3 py-2 text-center text-sm text-gray-700 lg:table-cell dark:border-gray-700 dark:text-gray-300">
            {{ result.creditUnit }}
          </td>

          <td
            class="hidden border-t border-gray-200 px-3 py-2 text-center text-sm text-gray-700 lg:table-cell dark:border-gray-700 dark:text-gray-300">
            {{ result.totalScore }}
          </td>

          <td
            class="border-t border-gray-200 px-3 py-2 text-center text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">
            <div class="sm:hidden">{{ result.grade }}</div>

            <div class="hidden sm:block">{{ result.grade }}</div>
          </td>

          <td
            class="hidden border-t border-gray-200 px-3 py-2 text-center text-sm text-gray-700 lg:table-cell dark:border-gray-700 dark:text-gray-300">
            {{ result.gradePoint }}
          </td>

          <td
            class="hidden border-t border-gray-200 px-3 py-2 text-center text-sm text-gray-700 lg:table-cell dark:border-gray-700 dark:text-gray-300" />
        </tr>

        <tr>
          <td
            class="hidden border-t border-gray-200 px-3 py-2 text-center text-sm font-bold text-gray-700 lg:table-cell dark:border-gray-700 dark:text-gray-300"
            colspan="3">
            TOTAL
          </td>

          <td
            class="hidden border-t border-gray-200 px-3 py-2 text-center text-sm font-bold text-gray-700 lg:table-cell dark:border-gray-700 dark:text-gray-300">
            {{ semester.formattedCreditUnitTotal }}
          </td>

          <td
            class="hidden border-t border-gray-200 px-3 py-2 text-center text-sm text-gray-700 lg:table-cell dark:border-gray-700 dark:text-gray-300" />

          <td
            class="hidden border-t border-gray-200 px-3 py-2 text-center text-sm text-gray-700 lg:table-cell dark:border-gray-700 dark:text-gray-300" />

          <td
            class="hidden border-t border-gray-200 px-3 py-2 text-center text-sm font-bold text-gray-700 lg:table-cell dark:border-gray-700 dark:text-gray-300">
            {{ semester.formattedGradePointTotal }}
          </td>

          <td
            class="hidden border-t border-gray-200 px-3 py-2 text-center text-sm font-bold text-gray-700 lg:table-cell dark:border-gray-700 dark:text-gray-300">
            {{ semester.formattedGPA }}
          </td>
        </tr>
      </tbody>
    </table>

    <div class="flex justify-around pb-2 text-right lg:hidden">
      <span>Total Credit Unit: {{ semester.formattedCreditUnitTotal }}</span>

      <span>Total Grade Point: {{ semester.formattedGradePointTotal }}</span>

      <span class="font-bold">GPA: {{ semester.formattedGPA }}</span>
    </div>
  </div>
</template>
