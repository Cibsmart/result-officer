<script lang="ts" setup>
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import InputError from "@/components/inputs/inputError.vue";
import { useForm } from "@inertiajs/vue3";
import SelectInput from "@/components/inputs/selectInput.vue";
import BaseFormSection from "@/components/forms/baseFormSection.vue";
import { useDepartments } from "@/composables/departments";
import AlignButton from "@/components/forms/alignButton.vue";

const form = useForm({
  department: "",
  department_object: { id: "" },
});

const submit = () => {
  form.transform((data) => ({ ...data, department: data.department_object.id })).post(route("graduand.store"));
};

const { departments, isLoading } = useDepartments();
</script>

<template>
  <BaseFormSection
    description="Select Department and input Registration Number"
    header="Vetting List">
    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <div class="flex w-full items-start space-x-4">
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

        <AlignButton>
          <PrimaryButton
            :disabled="form.processing"
            class="mt-1"
            >View
          </PrimaryButton>
        </AlignButton>
      </div>
    </form>
  </BaseFormSection>
</template>
