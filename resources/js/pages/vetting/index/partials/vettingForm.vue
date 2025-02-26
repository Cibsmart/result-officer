<script lang="ts" setup>
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import InputError from "@/components/inputs/inputError.vue";
import { useForm } from "@inertiajs/vue3";
import SelectInput from "@/components/inputs/selectInput.vue";
import BaseFormSection from "@/components/forms/baseFormSection.vue";
import { useDepartments } from "@/composables/departments";
import TextareaInput from "@/components/inputs/textareaInput.vue";

const form = useForm({
  department: "",
  registration_numbers: "",
  department_object: { id: "" },
});

const submit = () => {
  form.transform((data) => ({ ...data, department: data.department_object.id })).post(route("vettingEvent.store"));
};

const { departments, isLoading } = useDepartments(true);
</script>

<template>
  <BaseFormSection
    description="Select Department to View List of Possible Graduands"
    header="View List of Possible Graduands">
    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <div class="flex-1">
        <InputLabel
          for="department"
          value="Department" />

        <SelectInput
          v-if="!isLoading"
          id="department"
          v-model="form.department_object"
          :items="departments" />

        <SelectInput
          v-else
          id="department_loading"
          :items="departments" />

        <InputError
          :message="form.errors.department"
          class="mt-2" />
      </div>

      <div class="flex-1">
        <InputLabel
          for="registration_numbers"
          value="Registration Numbers" />

        <TextareaInput
          id="registration_numbers"
          v-model="form.registration_numbers"
          autocomplete="off"
          placeholder="Enter or paste list of registration numbers"
          required />

        <InputError :message="form.errors.registration_numbers" />
      </div>

      <PrimaryButton :disabled="form.processing"> Vet</PrimaryButton>
    </form>
  </BaseFormSection>
</template>
