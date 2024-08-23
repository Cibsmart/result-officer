<script lang="ts" setup>
import InputLabel from "@/components/inputs/inputLabel.vue";
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import InputError from "@/components/inputs/inputError.vue";
import { useForm } from "@inertiajs/vue3";
import SelectInput from "@/components/inputs/selectInput.vue";
import { SelectItem } from "@/types";
import BaseFormSection from "@/components/baseFormSection.vue";

defineProps<{
  departments: SelectItem[];
  sessions: SelectItem[];
  levels: SelectItem[];
}>();

const form = useForm({
  department: "",
  session: "",
  level: "",
});

const submit = () => {
  form.post(route("download.registrations.department-session-level.store"));
};
</script>

<template>
  <BaseFormSection
    description="Select Department, Session and Level to download course registration records"
    header="Download Course Registration Information">
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
        <InputLabel
          for="level"
          value="Session" />

        <SelectInput
          id="level"
          v-model="form.level"
          :items="levels"
          class="mt-1 block w-full" />

        <InputError
          :message="form.errors.level"
          class="mt-2" />
      </div>

      <div>
        <PrimaryButton :disabled="form.processing">Download</PrimaryButton>
      </div>
    </form>
  </BaseFormSection>
</template>
