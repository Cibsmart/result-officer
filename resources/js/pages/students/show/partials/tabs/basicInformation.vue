<script lang="ts" setup>
import DataList from "@/components/data/dataList.vue";
import DataItem from "@/components/data/dataItem.vue";
</script>

<template>
  <div>
    <div class="px-4 sm:px-0">
      <h3 class="text-base/7 font-semibold text-gray-900 dark:text-white">Student Information</h3>

      <p class="mt-1 max-w-2xl text-sm/6 text-gray-500 dark:text-gray-400">Personal and program details</p>
    </div>

    <div class="mt-6 border-t border-gray-100 dark:border-white/10">
      <DataList>
        <DataItem title="Full Name">IFEBUDE BARNABAS CHUKWUDIKE</DataItem>

        <DataItem title="Registration Number">EBSU/2009/51486</DataItem>

        <DataItem title="Department and Program">COMPUTER SCIENCE</DataItem>

        <DataItem title="Gender">MALE</DataItem>

        <DataItem title="Date of Birth">24th July, 1985</DataItem>

        <DataItem title="State and Local Government">ANAMBRA (ANAOCHA)</DataItem>

        <DataItem title="Entry Mode">UTME</DataItem>

        <DataItem title="Entry Session">2009/2010</DataItem>

        <DataItem title="Entry Level">100 LEVEL</DataItem>
      </DataList>
    </div>
  </div>
</template>
