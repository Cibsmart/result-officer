<script lang="ts" setup>
import InputLabel from "@/components/inputLabel.vue";
import PrimaryButton from "@/components/primaryButton.vue";
import InputError from "@/components/inputError.vue";
import { useForm } from "@inertiajs/vue3";
import SelectInput from "@/components/inputs/selectInput.vue";
import { SelectItem } from "@/types";

defineProps<{
  departments: SelectItem[];
  sessions: SelectItem[];
  semesters: SelectItem[];
}>();

const form = useForm({
  department: "",
  session: "",
  semester: "",
});

const submit = () => {
  form.post(route("download.results.department-session-semester.store"));
};
</script>

<template>
  <section>
    <header>
      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Download Result Information</h2>

      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        Select Department, Session and Semester to download result records
      </p>
    </header>

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
          for="semester"
          value="Session" />

        <SelectInput
          id="semester"
          v-model="form.semester"
          :items="semesters"
          class="mt-1 block w-full" />

        <InputError
          :message="form.errors.semester"
          class="mt-2" />
      </div>

      <div>
        <PrimaryButton :disabled="form.processing">Download</PrimaryButton>
      </div>
    </form>
  </section>
</template>
