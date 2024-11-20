<script lang="ts" setup>
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import InputError from "@/components/inputs/inputError.vue";
import { useForm } from "@inertiajs/vue3";
import SelectInput from "@/components/inputs/selectInput.vue";
import { SelectItem } from "@/types";
import BaseFormSection from "@/components/baseFormSection.vue";

defineProps<{
  departments: SelectItem[];
}>();

const form = useForm({
  department: "",
});

const submit = () => {
  form.post(route("vetting.view"));
};
</script>

<template>
  <BaseFormSection
    description="Select Department to View List of Possible Graduands"
    header="View List of Possible Graduands">
    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <div>
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

      <div>
        <PrimaryButton :disabled="form.processing">View</PrimaryButton>
      </div>
    </form>
  </BaseFormSection>
</template>
