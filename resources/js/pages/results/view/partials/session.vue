<script lang="ts" setup>
import Semester from "@/pages/results/view/partials/semester.vue";

withDefaults(
  defineProps<{
    session: App.Data.Results.SessionResultData;
    manageable?: true | false;
  }>(),
  {
    manageable: false,
  },
);
</script>

<template>
  <div class="-mx-4 mt-5 px-3 ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:ring-gray-600">
    <div class="min-w-full pt-2 text-center text-lg font-extrabold">{{ session.session }} SESSION</div>

    <template
      v-for="semester in session.semesterResults"
      :key="semester.id">
      <Semester
        :manageable
        :semester="semester" />
    </template>

    <div class="p-2 text-right">
      <span class="font-bold uppercase"> CGPA: {{ session.formattedCGPA }} </span>
    </div>
  </div>
</template>
