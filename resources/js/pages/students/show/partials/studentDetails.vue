<script lang="ts" setup>
import BaseTabPanel from "@/components/tabs/baseTabPanel.vue";
import BaseTabs from "@/components/tabs/baseTabs.vue";
import BasicInformation from "@/pages/students/show/partials/tabs/basicInformation.vue";
import StudentPageHeader from "@/pages/students/show/partials/studentPageHeader.vue";
import ResultInformation from "@/pages/students/show/partials/tabs/resultInformation.vue";

defineProps<{
  student: App.Data.Students.StudentData;
  results: App.Data.Results.StudentResultData;
  selectedIndex: number;
}>();

const tabs = [
  { name: "Details", href: "#", current: true },
  { name: "Results", href: "#", current: false },
  { name: "History", href: "#", current: false },
];
</script>

<template>
  <div class="relative pb-5 sm:pb-0">
    <StudentPageHeader :student="student.basic" />

    <div class="mt-3 sm:mt-4">
      <BaseTabs
        :selectedIndex="selectedIndex"
        :tabs="tabs">
        <BaseTabPanel>
          <BasicInformation :student="student" />
        </BaseTabPanel>

        <BaseTabPanel>
          <ResultInformation :results="results" />
        </BaseTabPanel>

        <BaseTabPanel>Tab 3</BaseTabPanel>
      </BaseTabs>
    </div>
  </div>
</template>
