<script lang="ts" setup>
import { Head, useForm } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import { BreadcrumbItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import BaseFormSection from "@/components/baseFormSection.vue";

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
      <BaseFormSection
        description="Click to Download All Departments from the Portal"
        header="Download Departments">
        <form
          class="mt-6 space-y-6"
          @submit.prevent="submit">
          <div>
            <PrimaryButton :disabled="form.processing">Download</PrimaryButton>
          </div>
        </form>
      </BaseFormSection>
    </BaseSection>
  </BasePage>
</template>
