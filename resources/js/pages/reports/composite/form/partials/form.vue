<script lang="ts" setup>
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import InputError from "@/components/inputs/inputError.vue";
import { useForm } from "@inertiajs/vue3";
import SelectInput from "@/components/inputs/selectInput.vue";
import { SelectItem } from "@/types";
import BaseFormSection from "@/components/baseFormSection.vue";

defineProps<{
  programs: SelectItem[];
  semesters: SelectItem[];
  sessions: SelectItem[];
  levels: SelectItem[];
}>();

const form = useForm({
  department: "",
  session: "",
  semester: "",
  level: "",
});

const submit = () => {
  form.post(route("composite.view"));
};
</script>

<template>
  <BaseFormSection
    description="Select Department, Session Semester and Level to view the Department Composite Sheet"
    header="Department Composite Sheet">
    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <div>
        <InputLabel
          for="program"
          value="Department" />

        <SelectInput
          id="program"
          v-model="form.department"
          :items="programs" />

        <InputError :message="form.errors.department" />
      </div>

      <div>
        <InputLabel
          for="session"
          value="Session" />

        <SelectInput
          id="session"
          v-model="form.session"
          :items="sessions" />

        <InputError :message="form.errors.session" />
      </div>

      <div>
        <InputLabel
          for="semester"
          value="Semester" />

        <SelectInput
          id="semester"
          v-model="form.semester"
          :items="semesters" />

        <InputError :message="form.errors.semester" />
      </div>

      <div>
        <InputLabel
          for="level"
          value="Level" />

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
        <PrimaryButton :disabled="form.processing">View</PrimaryButton>
      </div>
    </form>
  </BaseFormSection>
</template>
