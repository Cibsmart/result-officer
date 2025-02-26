<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import Breadcrumb from "@/components/breadcrumb.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";
import { BreadcrumbItem } from "@/types";
import VettingForm from "@/pages/vetting/index/partials/vettingForm.vue";

const pages: BreadcrumbItem[] = [
  { name: "Vetting", href: route("vettingEvent.index"), current: route().current("vettingEvent.index") },
];
</script>

<template>
  <Head title="Vetting Page" />

  <Breadcrumb :pages="pages" />

  <BaseHeader> Vetting Page</BaseHeader>

  <BasePage>
    <BaseSection>
      <VettingForm />
    </BaseSection>
  </BasePage>
</template>
