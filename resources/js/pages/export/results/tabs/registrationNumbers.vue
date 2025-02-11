<script lang="ts" setup>
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import InputError from "@/components/inputs/inputError.vue";
import { useForm } from "@inertiajs/vue3";
import BaseFormSection from "@/components/forms/baseFormSection.vue";
import FormGroup from "@/components/forms/formGroup.vue";
import AlignButton from "@/components/forms/alignButton.vue";
import TextareaInput from "@/components/inputs/textareaInput.vue";

const form = useForm({ registration_numbers: "" });

const submit = () => {
  form.post(route("export.results.registration-numbers.store"), { onSuccess: () => download() });
};

const download = () => {
  window.location.href = route("export.results.registration-numbers.download", {
    registration_numbers: form.registration_numbers,
  });
};
</script>

<template>
  <BaseFormSection
    description="Input students' Registration Numbers to export results records"
    header="Export Result Information">
    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <FormGroup>
        <div class="flex-1">
          <InputLabel
            for="registration_numbers"
            value="Registration Numbers" />

          <TextareaInput
            id="registration_numbers"
            v-model="form.registration_numbers"
            autocomplete="registration_numbers"
            autofocus
            placeholder="Enter comma separated List of Registration Numbers"
            required
            type="text" />

          <InputError :message="form.errors.registration_numbers" />
        </div>

        <AlignButton>
          <PrimaryButton :disabled="form.processing">Export</PrimaryButton>
        </AlignButton>
      </FormGroup>
    </form>
  </BaseFormSection>
</template>
