<script lang="ts" setup>
import { Head, useForm } from "@inertiajs/vue3";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import FormGroup from "@/components/forms/formGroup.vue";
import BaseFormSection from "@/components/forms/baseFormSection.vue";
import InputError from "@/components/inputs/inputError.vue";

const form = useForm({ file: null });

const submit = () => {
  form.post(route("import.final-results.store"));
};

const pages: BreadcrumbItem[] = [
  {
    name: "Final Result Import",
    href: route("import.final-results.index"),
    current: route().current("import.final-results.index"),
  },
];
</script>

<template>
  <Head title="Import Final Results" />

  <Breadcrumb :pages="pages" />

  <BaseHeader>Import Final (Reconciled) Results</BaseHeader>

  <BasePage>
    <BaseSection>
      <BaseFormSection
        description="Select the Reconciled Results Excel (.xlsx) File and click Upload"
        header="Upload Reconciled Results">
        <form
          class="mt-6 space-y-6"
          @submit.prevent="submit">
          <FormGroup>
            <div class="flex-1">
              <InputLabel
                for="file"
                value="Excel File" />

              <input
                type="file"
                @input="form.file = $event.target.files[0]" />

              <progress
                v-if="form.progress"
                :value="form.progress.percentage"
                max="100">
                {{ form.progress.percentage }}%
              </progress>

              <InputError :message="form.errors.file" />
            </div>

            <div>
              <PrimaryButton :disabled="form.processing">Upload</PrimaryButton>
            </div>
          </FormGroup>
        </form>
      </BaseFormSection>
    </BaseSection>
  </BasePage>
</template>
