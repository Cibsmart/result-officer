<script lang="ts" setup>
import DataList from "@/components/data/dataList.vue";
import DataItem from "@/components/data/dataItem.vue";
import SectionHeader from "@/components/sectionHeader.vue";

defineProps<{
  student: App.Data.Students.StudentData;
}>();
</script>

<template>
  <div>
    <SectionHeader description="Personal and program details">Basic Information</SectionHeader>

    <div class="mt-2 border-t border-gray-200 dark:border-white/10">
      <DataList>
        <!--        <DataItem title="Full Name">{{ student.basic.name }}</DataItem>-->

        <DataItem title="Registration Number">{{ student.basic.registrationNumber }}</DataItem>

        <DataItem title="Department and Program">{{ student.basic.department }}</DataItem>

        <DataItem title="Gender">{{ student.basic.gender }}</DataItem>

        <DataItem title="Date of Birth">{{ student.basic.birthDate }}</DataItem>

        <DataItem title="State and Local Government">
          {{ `${student.others.state} (${student.others.localGovernment})` }}
        </DataItem>

        <DataItem title="Entry Mode">{{ student.others.entryMode }}</DataItem>

        <DataItem title="Entry Session">{{ student.others.entrySession }}</DataItem>

        <DataItem title="Entry Level">{{ student.others.entryLevel }}</DataItem>
      </DataList>
    </div>
  </div>

  <div class="border-t border-gray-200 pt-8 dark:border-gray-700">
    <SectionHeader description="Other details">Other Information</SectionHeader>

    <div class="mt-2 border-t border-gray-200 dark:border-white/10">
      <DataList>
        <DataItem title="JAMB Registration Number">{{ student.others.jambRegistrationNumber }}</DataItem>

        <DataItem title="Email">{{ student.others.email }}</DataItem>

        <DataItem title="Phone Number">{{ student.others.phoneNumber }}</DataItem>
      </DataList>
    </div>
  </div>
</template>
