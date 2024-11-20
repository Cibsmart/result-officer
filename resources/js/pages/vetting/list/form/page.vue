<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import { BreadcrumbItem } from "@/types";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import Breadcrumb from "@/components/breadcrumb.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import GraduationForm from "@/pages/vetting/list/form/partials/form.vue";

defineProps<{
  departments: App.Data.Department.DepartmentListData;
}>();

const pages: BreadcrumbItem[] = [
  { name: "Vetting", href: route("vetting.form"), current: route().current("vetting.form") },
];
</script>

<template>
  <Head title="Vetting List" />

  <Breadcrumb :pages="pages" />

  <BaseHeader> View Vetting List</BaseHeader>

  <BasePage>
    <BaseSection>
      <GraduationForm :departments="departments.departments" />
    </BaseSection>
  </BasePage>
</template>
