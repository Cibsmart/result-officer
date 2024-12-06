<script lang="ts" setup>
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import TextInput from "@/components/inputs/textInput.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import InputError from "@/components/inputs/inputError.vue";
import { useForm } from "@inertiajs/vue3";
import BaseFormSection from "@/components/forms/baseFormSection.vue";

const form = useForm({
  registration_number: "",
});

const submit = () => {
  form.post(route("results.view"));
};
</script>

<template>
  <BaseFormSection
    description="Input student's registration number to view results"
    header="Result Information">
    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <div>
        <InputLabel
          for="registration_number"
          value="Registration Number" />

        <TextInput
          id="registration_number"
          v-model="form.registration_number"
          autocomplete="registration_number"
          autofocus
          class="mt-1 block w-full"
          placeholder="EBSU/2009/51486"
          required
          type="text" />

        <InputError
          :message="form.errors.registration_number"
          class="mt-2" />
      </div>

      <div>
        <PrimaryButton :disabled="form.processing">View</PrimaryButton>
      </div>
    </form>
  </BaseFormSection>
</template>
