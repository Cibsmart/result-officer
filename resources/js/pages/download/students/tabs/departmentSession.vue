<script lang="ts" setup>
import InputLabel from "@/components/inputLabel.vue";
import PrimaryButton from "@/components/primaryButton.vue";
import InputError from "@/components/inputError.vue";
import { useForm } from "@inertiajs/vue3";
import SelectInput from "@/components/inputs/selectInput.vue";
import useDepartment from "@/composables/useDepartment.js";
import useSession from "@/composables/useSession.js";

const departments = useDepartment.getDepartments();
const sessions = useSession.getSessions();

const form = useForm({
  department: "",
  session: "",
});

const submit = () => {
  form.post(route("download.students.department-session.store"));
};
</script>

<template>
  <section>
    <header>
      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Download Students Information</h2>

      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        Select Department and Session to download students' records
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
        <PrimaryButton :disabled="form.processing">View</PrimaryButton>
      </div>
    </form>
  </section>
</template>
