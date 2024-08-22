<script lang="ts" setup>
import { Head, useForm } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BaseSection from "@/components/baseSection.vue";
import { BreadcrumbItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";
import PrimaryButton from "@/components/primaryButton.vue";

const pages: BreadcrumbItem[] = [
  {
    name: "Download Departments",
    href: route("download.departments.page"),
    current: route().current("download.departments.page"),
  },
];

const form = useForm({});

const submit = () => {
  form.post(route("download.departments.store"));
};
</script>

<template>
  <Head title="Download Student Record" />

  <Breadcrumb :pages="pages" />

  <BaseHeader>Download Student Record</BaseHeader>

  <BasePage>
    <BaseSection>
      <section>
        <header>
          <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Download Departments</h2>

          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Click to Download All Departments from the Portal</p>
        </header>

        <form
          class="mt-6 space-y-6"
          @submit.prevent="submit">
          <div>
            <PrimaryButton :disabled="form.processing">Download</PrimaryButton>
          </div>
        </form>
      </section>
    </BaseSection>
  </BasePage>
</template>
