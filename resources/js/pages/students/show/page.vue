<script lang="ts" setup>
import { BreadcrumbItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";
import { Head, useForm } from "@inertiajs/vue3";
import TextInput from "@/components/inputs/textInput.vue";
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import BaseFormSection from "@/components/forms/baseFormSection.vue";
import InputError from "@/components/inputs/inputError.vue";
import FormGroup from "@/components/forms/formGroup.vue";
import AlignButton from "@/components/forms/alignButton.vue";

const pages: BreadcrumbItem[] = [
  { name: "Student", href: route("students.show"), current: route().current("students.show") },
];

const form = useForm({ registration_number: "" });

const submit = () => form.post(route("students.store"));
</script>

<template>
  <Head title="Student Page" />

  <Breadcrumb :pages="pages" />

  <BaseHeader>Student Page</BaseHeader>

  <BasePage>
    <BaseSection>
      <BaseFormSection
        description="Input student's registration number to view page"
        header="Student Information">
        <form
          class="mt-6 space-y-6"
          @submit.prevent="submit">
          <FormGroup>
            <div class="flex-1">
              <InputLabel
                for="registration_number"
                value="Registration Number" />

              <TextInput
                id="registration_number"
                v-model="form.registration_number"
                autocomplete="registration_number"
                autofocus
                placeholder="EBSU/2009/51486"
                required
                type="text" />

              <InputError :message="form.errors.registration_number" />
            </div>

            <AlignButton>
              <PrimaryButton :disabled="form.processing">View</PrimaryButton>
            </AlignButton>
          </FormGroup>
        </form>
      </BaseFormSection>
    </BaseSection>

    <BaseSection>Table</BaseSection>
  </BasePage>
</template>
