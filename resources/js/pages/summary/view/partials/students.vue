<script lang="ts" setup>
defineProps<{
  students: Array<App.Data.Summary.StudentResultSummaryData>;
}>();
</script>

<template>
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
          v-for="(student, index) in students"
          :key="student.student.id">
          <td
            class="hidden border-t border-gray-200 px-3 py-2 text-center text-sm text-gray-700 lg:table-cell dark:border-gray-700 dark:text-gray-300">
            {{ index + 1 }}
          </td>

          <td class="relative border-t border-gray-200 py-2 pl-4 pr-3 text-sm sm:pl-6 dark:border-gray-700">
            {{ student.student.name }}
          </td>

          <td class="relative border-t border-gray-200 py-2 pl-4 pr-3 text-sm sm:pl-6 dark:border-gray-700">
            {{ student.student.registrationNumber }}
          </td>

          <td
            class="border-t border-gray-200 px-3 py-2 text-center text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">
            {{ student.fcgpa }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped></style>
