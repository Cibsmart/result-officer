<script lang="ts" setup>
import InputLabel from "@/components/inputLabel.vue";
import PrimaryButton from "@/components/primaryButton.vue";
import InputError from "@/components/inputError.vue";
import { useForm } from "@inertiajs/vue3";
import SelectInput from "@/components/inputs/selectInput.vue";
import SelectInputSearchable from "@/components/inputs/selectInputSearchable.vue";
import { SelectItem } from "@/types";

defineProps<{
  sessions: SelectItem[];
  courses: SelectItem[];
}>();

const form = useForm({
  session: "",
  course: "",
});

const submit = () => {
  form.post(route("download.results.session-course.store"));
};
</script>

<template>
  <section>
    <header>
      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Download Result Information</h2>

      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Select session and course to download result records</p>
    </header>

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
        <InputLabel
          for="course"
          value="Course" />

        <SelectInputSearchable
          id="course"
          v-model="form.course"
          :items="courses"
          class="mt-1 block w-full"
          name="courses" />

        <InputError
          :message="form.errors.course"
          class="mt-2" />
      </div>

      <div>
        <PrimaryButton :disabled="form.processing">Download</PrimaryButton>
      </div>
    </form>
  </section>
</template>
