<script lang="ts" setup>
import PrimaryButton from "@/components/primaryButton.vue";
import InputLabel from "@/components/inputLabel.vue";
import InputError from "@/components/inputError.vue";
import { useForm } from "@inertiajs/vue3";
import SelectInput from "@/components/inputs/selectInput.vue";
import { SelectItem } from "@/types";

defineProps<{
  departments: SelectItem[];
  sessions: SelectItem[];
  levels: SelectItem[];
}>();

const form = useForm({
  department_id: "",
  session_id: "",
  level_id: "",
});

const submit = () => {
  form.post(route("summary.view"));
};
</script>

<template>
  <section>
    <header>
      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Departmental Result Summary Information</h2>

      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        Select <span class="font-bold italic">Department</span>

        , <span class="font-bold italic">Session</span>

        and <span class="font-bold italic">Level</span> to view the departmental result summary
      </p>
    </header>

    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <div>
        <InputLabel
          for="department_id"
          value="Department" />

        <SelectInput
          id="department_id"
          v-model="form.department_id"
          :items="departments"
          class="mt-1 block w-full" />

        <InputError
          :message="form.errors.department_id"
          class="mt-2" />
      </div>

      <div>
        <InputLabel
          for="session_id"
          value="Session" />

        <SelectInput
          id="session_id"
          v-model="form.session_id"
          :items="sessions"
          class="mt-1 block w-full" />

        <InputError
          :message="form.errors.session_id"
          class="mt-2" />
      </div>

      <div>
        <InputLabel
          for="level_id"
          value="Level" />

        <SelectInput
          id="level_id"
          v-model="form.level_id"
          :items="levels"
          class="mt-1 block w-full" />

        <InputError
          :message="form.errors.level_id"
          class="mt-2" />
      </div>

      <div>
        <PrimaryButton :disabled="form.processing">View</PrimaryButton>
      </div>
    </form>
  </section>
</template>
