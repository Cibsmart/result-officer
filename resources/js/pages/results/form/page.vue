<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import StudentResultForm from "@/pages/results/form/partials/form.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import { BreadcrumbItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";

const pages: BreadcrumbItem[] = [
  { name: "Result", href: route("results.form"), current: route().current("results.form") },
];
</script>

<template>
  <Head title="View Student Result" />

  <Breadcrumb :pages="pages" />

  <BaseHeader> View Student Results</BaseHeader>

  <BasePage>
    <BaseSection>
      <StudentResultForm class="max-w-xl" />
    </BaseSection>
  </BasePage>
</template>
