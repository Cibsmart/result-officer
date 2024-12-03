<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";
import { BreadcrumbItem } from "@/types";
import ClearedForm from "@/pages/reports/cleared/index/partials/clearedForm.vue";

defineProps<{
  departments: App.Data.Department.DepartmentListData;
}>();

const pages: BreadcrumbItem[] = [
  { name: "Cleared", href: route("department.cleared.index"), current: route().current("department.cleared.index") },
];
</script>

<template>
  <Head title="Cleared Students" />

  <Breadcrumb :pages="pages" />

  <BaseHeader> Department Cleared Students</BaseHeader>

  <BasePage>
    <BaseSection>
      <ClearedForm :departments="departments.data" />
    </BaseSection>
  </BasePage>
</template>
