<script lang="ts" setup>
import InputLabel from "@/components/inputs/inputLabel.vue";
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import InputError from "@/components/inputs/inputError.vue";
import { useForm } from "@inertiajs/vue3";
import SelectInput from "@/components/inputs/selectInput.vue";
import { SelectItem } from "@/types";
import BaseFormSection from "@/components/forms/baseFormSection.vue";
import FormGroup from "@/components/forms/formGroup.vue";
import AlignButton from "@/components/forms/alignButton.vue";

defineProps<{
  departments: SelectItem[];
  sessions: SelectItem[];
}>();

const form = useForm({
  department: "",
  session: "",
});

const submit = () => {
  form.post(route("download.registrations.department-session.store"));
};
</script>

<template>
  <BaseFormSection
    description="Select Department and Entry Session to download course registration records"
    header="Download Course Registration Information">
    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <FormGroup>
        <div class="flex-1">
          <InputLabel
            for="department"
            value="Department" />

          <SelectInput
            id="department"
            v-model="form.department"
            :items="departments"
            class="mt-1 block w-full" />

          <InputError :message="form.errors.department" />
        </div>

        <div class="flex-1">
          <InputLabel
            for="session"
            value="Entry Session" />

          <SelectInput
            id="session"
            v-model="form.session"
            :items="sessions" />

          <InputError :message="form.errors.session" />
        </div>

        <AlignButton>
          <PrimaryButton
            :disabled="form.processing"
            class="mt-1">
            Download
          </PrimaryButton>
        </AlignButton>
      </FormGroup>
    </form>
  </BaseFormSection>
</template>
