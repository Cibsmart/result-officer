<script lang="ts" setup>
import DataList from "@/components/data/dataList.vue";
import DataItem from "@/components/data/dataItem.vue";
import SectionHeader from "@/components/sectionHeader.vue";
</script>

<template>
  <div>
    <SectionHeader description="Personal and program details">Basic Information</SectionHeader>

    <div class="mt-2 border-t border-gray-200 dark:border-white/10">
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

  <div class="border-t border-gray-200 pt-8 dark:border-gray-700">
    <SectionHeader description="Other details">Other Information</SectionHeader>

    <div class="mt-2 border-t border-gray-200 dark:border-white/10">
      <DataList>
        <DataItem title="JAMB Registration Number">950105245HB</DataItem>

        <DataItem title="Email">bifebude@gmail.com</DataItem>

        <DataItem title="Phone Number">07032434429</DataItem>
      </DataList>
    </div>
  </div>
</template>
