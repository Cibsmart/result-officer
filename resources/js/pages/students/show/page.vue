<script lang="ts" setup>
import { BreadcrumbItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";
import { Head } from "@inertiajs/vue3";
import StudentShowForm from "@/pages/students/show/partials/studentShowForm.vue";
import StudentDetails from "@/pages/students/show/partials/studentDetails.vue";

const pages: BreadcrumbItem[] = [
  { name: "Student", href: route("students.show"), current: route().current("students.show") },
];
</script>

<template>
  <Head title="Student Page" />

  <Breadcrumb :pages="pages" />

  <BaseHeader>Student Page</BaseHeader>

  <BasePage>
    <BaseSection>
      <StudentShowForm />
    </BaseSection>

    <BaseSection>
      <StudentDetails />
    </BaseSection>
  </BasePage>
</template>
