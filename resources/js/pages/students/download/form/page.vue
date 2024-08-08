<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import RegistrationNumberForm from "@/pages/students/download/form/partials/form.vue";
import BaseSection from "@/components/baseSection.vue";
import { BreadcrumbItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";

const pages: BreadcrumbItem[] = [
  { name: "Student Download", href: route("download.student.form"), current: route().current("download.student.form") },
];
</script>

<template>
  <Head title="Download Student Record" />

  <Breadcrumb :pages="pages" />

  <BaseHeader>Download Student Record</BaseHeader>

  <BasePage>
    <BaseSection>
      <RegistrationNumberForm class="max-w-xl" />
    </BaseSection>
  </BasePage>
</template>
