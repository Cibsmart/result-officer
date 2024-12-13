<script lang="ts" setup>
import { BreadcrumbItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";
import { Head } from "@inertiajs/vue3";

const pages: BreadcrumbItem[] = [
  { name: "Student", href: route("students.index"), current: route().current("students.index") },
];
</script>

<template>
  <Head title="Students Page" />

  <Breadcrumb :pages="pages" />

  <BaseHeader>Students Page</BaseHeader>

  <BasePage>Hello</BasePage>
</template>
