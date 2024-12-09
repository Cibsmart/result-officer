<script lang="ts" setup>
import BaseTabPanel from "@/components/tabs/baseTabPanel.vue";
import BaseTabs from "@/components/tabs/baseTabs.vue";
import BasicInformation from "@/pages/students/show/partials/tabs/basicInformation.vue";
import StudentPageHeader from "@/pages/students/show/partials/studentPageHeader.vue";

const tabs = [
  { name: "Details", href: "#", current: true },
  { name: "Results", href: "#", current: false },
  { name: "History", href: "#", current: false },
];

const selectedIndex = 0;
</script>

<template>
  <div class="relative pb-5 sm:pb-0">
    <StudentPageHeader />

    <div class="broder mt-3 border-gray-400 sm:mt-4">
      <BaseTabs
        :selectedIndex="selectedIndex"
        :tabs="tabs">
        <BaseTabPanel>
          <BasicInformation />
        </BaseTabPanel>

        <BaseTabPanel>Tab 2</BaseTabPanel>

        <BaseTabPanel>Tab 3</BaseTabPanel>
      </BaseTabs>
    </div>
  </div>
</template>
