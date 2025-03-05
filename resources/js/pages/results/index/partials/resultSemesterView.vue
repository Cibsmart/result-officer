<script lang="ts" setup>
import BaseTD from "@/components/tables/baseTD.vue";
import BaseTBody from "@/components/tables/baseTBody.vue";
import BaseTR from "@/components/tables/baseTR.vue";
import BaseTH from "@/components/tables/baseTH.vue";
import BaseTHead from "@/components/tables/baseTHead.vue";
import BaseTable from "@/components/tables/baseTable.vue";
import { computed } from "vue";
import HamburgerMenu from "@/components/hamburger/hamburgerMenu.vue";
import HamburgerMenuItem from "@/components/hamburger/hamburgerMenuItem.vue";
import { usePage } from "@inertiajs/vue3";

const props = withDefaults(
  defineProps<{
    semester: App.Data.Results.SemesterResultData;
    manageable?: true | false;
  }>(),
  {
    manageable: false,
  },
);

const emit = defineEmits<{
  (e: "openEditResult", result: App.Data.Results.ResultData): void;
  (e: "openDeleteResult", result: App.Data.Results.ResultData): void;
}>();

const title = computed(() => `${props.semester.semester} SEMESTER`);

const isAdmin = usePage().props.user?.isAdmin;
</script>

<template>
  <BaseTable :title="title">
    <BaseTHead>
      <BaseTH>SN</BaseTH>

      <BaseTH
        mobile
        position="left">
        Course Code
      </BaseTH>

      <BaseTH> Course Title</BaseTH>

      <BaseTH> Credit Unit</BaseTH>

      <BaseTH> Score</BaseTH>

      <BaseTH mobile> Grade</BaseTH>

      <BaseTH>GP</BaseTH>

      <BaseTH>GPA</BaseTH>

      <template v-if="manageable">
        <BaseTH>Date Updated</BaseTH>

        <BaseTH v-if="isAdmin">Actions</BaseTH>
      </template>
    </BaseTHead>

    <BaseTBody>
      <BaseTR
        v-for="(result, index) in semester.results"
        :key="result.id">
        <BaseTD>{{ index + 1 }}</BaseTD>

        <BaseTD
          mobile
          position="left">
          <div class="font-medium text-gray-900 dark:text-white">{{ result.courseCode }}</div>

          <div class="mt-1 flex flex-col text-gray-700 sm:block lg:hidden dark:text-gray-300">
            <span>{{ result.courseTitle }} </span>

            <span class="hidden sm:inline"> || </span>

            <span>
              <span>Unit: {{ result.creditUnit }} |</span>

              <span>| Score: {{ result.totalScore }} </span>
            </span>
          </div>
        </BaseTD>

        <BaseTD position="left">{{ result.courseTitle }}</BaseTD>

        <BaseTD>{{ result.creditUnit }}</BaseTD>

        <BaseTD>{{ result.totalScore }}</BaseTD>

        <BaseTD mobile>{{ result.grade }}</BaseTD>

        <BaseTD>{{ result.gradePoint }}</BaseTD>

        <BaseTD />

        <template v-if="manageable">
          <BaseTD>{{ result.dateUpdated }}</BaseTD>

          <BaseTD v-if="isAdmin">
            <HamburgerMenu orientation="horizontal">
              <HamburgerMenuItem
                type="button"
                @click="emit('openEditResult', result)">
                Edit
              </HamburgerMenuItem>

              <HamburgerMenuItem
                type="button"
                @click="emit('openDeleteResult', result)">
                Delete
              </HamburgerMenuItem>
            </HamburgerMenu>
          </BaseTD>
        </template>
      </BaseTR>

      <BaseTR>
        <BaseTD colspan="3">TOTAL</BaseTD>

        <BaseTD>{{ semester.formattedCreditUnitTotal }}</BaseTD>

        <BaseTD />

        <BaseTD />

        <BaseTD>{{ semester.formattedGradePointTotal }}</BaseTD>

        <BaseTD>{{ semester.formattedGPA }}</BaseTD>

        <template v-if="manageable">
          <BaseTD />

          <BaseTD />
        </template>
      </BaseTR>
    </BaseTBody>
  </BaseTable>

  <div class="flex justify-around pb-2 text-right lg:hidden">
    <span>Total Credit Unit: {{ semester.formattedCreditUnitTotal }}</span>

    <span>Total Grade Point: {{ semester.formattedGradePointTotal }}</span>

    <span class="font-bold">GPA: {{ semester.formattedGPA }}</span>
  </div>
</template>
