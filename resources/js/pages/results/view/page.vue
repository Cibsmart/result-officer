<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import StudentResults from "@/pages/results/view/partials/table.vue";
import BaseSection from "@/components/baseSection.vue";
import { BreadcrumbItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";

defineProps<{
  student?: App.Data.Students.StudentData;
  results?: App.Data.Results.StudentResultData;
}>();

const pages: BreadcrumbItem[] = [
  { name: "Result Form", href: route("results.form"), current: route().current("results.form") },
  { name: "Result View", href: route("results.view"), current: route().current("results.view") },
];
</script>

<template>
  <Head title="View Student Result" />

  <Breadcrumb :pages="pages" />

  <BaseHeader> View Student Results</BaseHeader>

  <BasePage>
    <BaseSection> </BaseSection>
  </BasePage>
</template>
