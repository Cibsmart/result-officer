<script lang="ts" setup>
import InputLabel from "@/components/inputs/inputLabel.vue";
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import InputError from "@/components/inputs/inputError.vue";
import { useForm } from "@inertiajs/vue3";
import SelectInput from "@/components/inputs/selectInput.vue";
import { SelectItem } from "@/types";
import BaseFormSection from "@/components/baseFormSection.vue";

defineProps<{
  sessions: SelectItem[];
}>();

const form = useForm({
  session: "",
});

const submit = () => {
  form.post(route("download.students.session.store"));
};
</script>

<template>
  <BaseFormSection
    description="Select Session to download their records"
    header="Download Students Information">
    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <div>
        <InputLabel
          for="session"
          value="Session" />

        <SelectInput
          id="session"
          v-model="form.session"
          :items="sessions"
          class="mt-1 block w-full" />

        <InputError
          :message="form.errors.session"
          class="mt-2" />
      </div>

      <div>
        <PrimaryButton :disabled="form.processing">View</PrimaryButton>
      </div>
    </form>
  </BaseFormSection>
</template>
