<script lang="ts" setup>
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import InputError from "@/components/inputs/inputError.vue";
import { useForm } from "@inertiajs/vue3";
import SelectInput from "@/components/inputs/selectInput.vue";
import { SelectItem } from "@/types";
import BaseFormSection from "@/components/forms/baseFormSection.vue";
import FormGroup from "@/components/forms/formGroup.vue";
import { useMonths } from "@/composables/months";
import { useYears } from "@/composables/year";

defineProps<{
  departments: SelectItem[];
}>();

const form = useForm({
  department: "",
  year: "",
  month: "",
  department_object: { id: "" },
  month_object: { id: "" },
  year_object: { id: "" },
});

const submit = () => {
  form
    .transform((data) => ({
      ...data,
      department: data.department_object.id,
      month: data.month_object.id,
      year: data.year_object.id,
    }))
    .post(route("department.cleared.store"));
};

const { years } = useYears();

const { months } = useMonths();
</script>

<template>
  <BaseFormSection
    description="Select Department, Year and Month to View List of Cleared Students"
    header="View Cleared Student">
    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <div class="flex-1">
        <InputLabel
          for="department"
          value="Department" />

        <SelectInput
          id="department"
          v-model="form.department_object"
          :items="departments" />

        <InputError
          :message="form.errors.department"
          class="mt-2" />
      </div>

      <FormGroup>
        <div class="flex-1">
          <InputLabel
            for="year"
            value="Year" />

          <SelectInput
            id="year"
            v-model="form.year_object"
            :items="years" />

          <InputError
            :message="form.errors.year"
            class="mt-2" />
        </div>

        <div class="flex-1">
          <InputLabel
            for="month"
            value="Month" />

          <SelectInput
            id="month"
            v-model="form.month_object"
            :items="months"
            class="mt-1 block w-full" />

          <InputError
            :message="form.errors.month"
            class="mt-2" />
        </div>

        <div class="flex-col">
          <div>&nbsp;</div>

          <PrimaryButton
            :disabled="form.processing"
            class="mt-1">
            View
          </PrimaryButton>
        </div>
      </FormGroup>
    </form>
  </BaseFormSection>
</template>
