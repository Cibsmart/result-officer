<script lang="ts" setup>
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import InputError from "@/components/inputs/inputError.vue";
import { useForm } from "@inertiajs/vue3";
import SelectInput from "@/components/inputs/selectInput.vue";
import { SelectItem } from "@/types";
import BaseFormSection from "@/components/forms/baseFormSection.vue";

defineProps<{
  departments: SelectItem[];
}>();

const form = useForm({
  department: "",
});

const submit = () => {
  form.get(route("vetting.index", { department: form.department }));
};
</script>

<template>
  <BaseFormSection
    description="Select Department to View List of Possible Graduands"
    header="View List of Possible Graduands">
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
