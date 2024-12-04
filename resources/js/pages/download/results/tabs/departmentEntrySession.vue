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
}>();

const form = useForm({
  department: "",
  session: "",
});

const submit = () => {
  form.post(route("download.results.department-session.store"));
};
</script>

<template>
  <BaseFormSection
    description="Select Department and Entry Session to download results records"
    header="Download Result Information">
    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <div class="w-full items-start md:flex md:space-x-4">
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
            for="session"
            value="Entry Session" />

          <SelectInput
            id="session"
            v-model="form.session"
            :items="sessions"
            class="mt-1 block w-full" />

          <InputError
            :message="form.errors.session"
            class="mt-2" />
        </div>

        <div class="flex-col">
          <div class="sm:hidden md:block">&nbsp;</div>

          <PrimaryButton
            :disabled="form.processing"
            class="mt-1">
            Download
          </PrimaryButton>
        </div>
      </div>
    </form>
  </BaseFormSection>
</template>
