<script lang="ts" setup>
import { computed } from "vue";
import { Head } from "@inertiajs/vue3";
import { BreadcrumbItem } from "@/types";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import Breadcrumb from "@/components/breadcrumb.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import VettingForm from "@/pages/vetting/list/index/partials/vettingForm.vue";
import StudentList from "@/pages/vetting/list/index/partials/studentList.vue";

const props = defineProps<{
  departments: App.Data.Department.DepartmentListData;
  data: App.Data.Vetting.VettingListData;
  steps: App.Data.Vetting.VettingStepListData;
}>();

const hasData = computed(() => props.data !== null);

const pages: BreadcrumbItem[] = [
  { name: "Vetting", href: route("vetting.index"), current: route().current("vetting.index") },
];
</script>

<template>
  <Head title="Vetting Page" />

  <Breadcrumb :pages="pages" />

  <BaseHeader> View Vetting List</BaseHeader>

  <BasePage>
    <BaseSection>
      <VettingForm :departments="departments.data" />
    </BaseSection>

    <BaseSection v-if="hasData">
      <StudentList
        :data="data"
        :steps="steps" />
    </BaseSection>
  </BasePage>
</template>
