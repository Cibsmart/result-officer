<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import { BreadcrumbItem } from "@/types";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import Breadcrumb from "@/components/breadcrumb.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import VettingForm from "@/pages/vetting/list/index/partials/vettingForm.vue";
import StudentList from "@/pages/vetting/list/index/partials/studentList.vue";
import { PaginatedVettingListData } from "@/types/paginate";

defineProps<{
  departments: App.Data.Department.DepartmentListData;
  clearance: App.ViewModels.Clearance.ClearanceFormPage;
  steps: App.Data.Vetting.VettingStepListData;
  department: App.Data.Department.DepartmentInfoData;
  data: PaginatedVettingListData | null;
}>();

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

    <StudentList
      v-if="data !== null"
      :clearance="clearance"
      :department="department"
      :paginated="data"
      :steps="steps" />
  </BasePage>
</template>
