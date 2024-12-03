<script lang="ts" setup>
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import InputError from "@/components/inputs/inputError.vue";
import { useForm } from "@inertiajs/vue3";
import SelectInput from "@/components/inputs/selectInput.vue";
import { SelectItem } from "@/types";
import BaseFormSection from "@/components/baseFormSection.vue";
import { computed } from "vue";

defineProps<{
  departments: SelectItem[];
}>();

const form = useForm({ department: "", year: "" });

const submit = () => {
  form.post(route("department.cleared.store"));
};

const years = computed(() => {
  const startYear = 2009;
  const currentYear = new Date().getFullYear();
  return [
    { id: 0, name: "Select Year" },
    ...Array.from({ length: currentYear - startYear + 2 }, (_, i) => {
      const year = currentYear - i;
      return { id: year, name: year.toString() };
    }),
  ];
});
</script>

<template>
  <BaseFormSection
    description="Select Department and Year to View List of Cleared Students"
    header="View Cleared Student">
    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <div class="flex w-full items-start space-x-4">
        <div class="flex-1">
          <InputLabel
            for="department"
            value="Department" />

          <SelectInput
            id="department"
            v-model="form.department"
            :items="departments"
            class="mt-1 block w-full" />

          <InputError
            :message="form.errors.department"
            class="mt-2" />
        </div>

        <div class="flex-1">
          <InputLabel
            for="year"
            value="Year" />

          <SelectInput
            id="year"
            v-model="form.year"
            :items="years"
            class="mt-1 block w-full" />

          <InputError
            :message="form.errors.year"
            class="mt-2" />
        </div>

        <div class="flex-col">
          <div>&nbsp;</div>

          <PrimaryButton
            :disabled="form.processing"
            class="mt-1"
            >View
          </PrimaryButton>
        </div>
      </div>
    </form>
  </BaseFormSection>
</template>
