<script lang="ts" setup>
import InputLabel from "@/components/inputs/inputLabel.vue";
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import InputError from "@/components/inputs/inputError.vue";
import { useForm } from "@inertiajs/vue3";
import SelectInput from "@/components/inputs/selectInput.vue";
import { SelectItem } from "@/types";
import SelectInputSearchable from "@/components/inputs/selectInputSearchable.vue";
import BaseFormSection from "@/components/baseFormSection.vue";

defineProps<{
  sessions: SelectItem[];
  courses: SelectItem[];
}>();

const form = useForm({
  session: "",
  course: "",
});

const submit = () => {
  form.post(route("download.registrations.session-course.store"));
};
</script>

<template>
  <BaseFormSection
    description="Select Session and Course to download course registration records"
    header="Download Course Registration Information">
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
  </BaseFormSection>
</template>
