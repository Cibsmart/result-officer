<script lang="ts" setup>
import EmptyState from "@/components/emptyState.vue";
import IconLink from "@/components/links/iconLink.vue";
import { computed } from "vue";
import PrimaryButtonSmall from "@/components/buttons/primaryButtonSmall.vue";

const props = defineProps<{
  students: App.Data.Cleared.ClearedStudentListData;
}>();

const hasRows = computed(() => props.students.data.length > 0);
</script>

<template>
  <div class="rounded px-4 py-4 sm:px-6 lg:px-8 dark:bg-gray-900">
    <div
      class="mt-1 divide-y divide-solid divide-gray-300 ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:divide-gray-600 dark:ring-gray-600">
      <div class="grid grid-flow-col">
        <div class="p-2">
          FACULTY: <span class="font-bold text-black dark:text-white">{{ students.faculty.name }}</span>
        </div>
      </div>

      <div class="grid grid-flow-col">
        <div class="p-2">
          DEPARTMENT: <span class="font-bold text-black dark:text-white">{{ students.department.name }}</span>
        </div>
      </div>
    </div>

    <div>
      <template v-if="hasRows">
        <div class="-mx-4 mt-4 overflow-auto ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:ring-gray-600">
          <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
            <thead>
              <tr>
                <th
                  class="hidden px-3 py-2 text-center text-sm font-semibold text-gray-900 lg:table-cell dark:text-white"
                  scope="col">
                  SN
                </th>

                <th
                  class="py-2 text-left text-sm font-semibold text-gray-900 dark:text-white"
                  scope="col">
                  NAME
                </th>

                <th
                  class="hidden py-2 text-center text-sm font-semibold text-gray-900 lg:table-cell dark:text-white"
                  scope="col">
                  REGISTRATION NUMBER
                </th>

                <th
                  class="hidden py-2 text-center text-sm font-semibold text-gray-900 lg:table-cell dark:text-white"
                  scope="col">
                  FCGPA
                </th>

                <th
                  class="hidden py-2 text-center text-sm font-semibold text-gray-900 lg:table-cell dark:text-white"
                  scope="col">
                  DATE CLEARED
                </th>

                <th
                  class="py-2 text-center text-sm font-semibold text-gray-900 dark:text-white"
                  scope="col">
                  ACTIONS
                </th>
              </tr>
            </thead>

            <tbody>
              <template
                v-for="(student, index) in students.data"
                :key="student.id">
                <tr>
                  <td
                    class="hidden border-t border-gray-200 px-3 py-2 text-center text-sm text-gray-700 lg:table-cell dark:border-gray-700 dark:text-gray-300">
                    {{ index + 1 }}
                  </td>

                  <td class="relative border-t border-gray-200 py-2 text-left text-sm dark:border-gray-700">
                    {{ student.name }}
                  </td>

                  <td class="relative border-t border-gray-200 py-2 text-center text-sm dark:border-gray-700">
                    {{ student.registrationNumber }}
                  </td>

                  <td class="relative border-t border-gray-200 py-2 text-center text-sm dark:border-gray-700">
                    {{ student.fcgpa }}
                  </td>

                  <td
                    class="border-t border-gray-200 px-3 py-2 text-center text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">
                    {{ student.dateCleared }}
                  </td>

                  <td
                    class="border-t border-gray-200 px-3 py-2 text-center text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">
                    <PrimaryButtonSmall> graduate</PrimaryButtonSmall>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </template>

      <EmptyState
        v-else
        description="Get started by clearing students that are ready for graduation"
        title="No Cleared Student Found in the selected year">
        <IconLink :href="route('vetting.index', { department: students.department.id })">Vet Students</IconLink>
      </EmptyState>
    </div>
  </div>
</template>
