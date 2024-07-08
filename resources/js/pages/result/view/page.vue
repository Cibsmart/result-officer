<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import StudentResultForm from "@/pages/result/view/partials/studentResultForm.vue";
import StudentResults from "@/pages/result/view/partials/studentResults.vue";
import BaseSection from "@/components/baseSection.vue";
import { BreadcrumbItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";

const pages: BreadcrumbItem[] = [
  { name: "Result", href: route("result.page"), current: route().current("result.page") },
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

    <BaseSection>
      <StudentResults class="max-w-xl" />
    </BaseSection>
  </BasePage>
</template>
