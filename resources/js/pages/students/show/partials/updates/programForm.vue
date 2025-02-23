<script lang="ts" setup>
import BaseFormSection from "@/components/forms/baseFormSection.vue";
import TextInput from "@/components/inputs/textInput.vue";
import InputError from "@/components/inputs/inputError.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import { useForm } from "@inertiajs/vue3";
import SecondaryButton from "@/components/buttons/secondaryButton.vue";
import { computed, ref, watch } from "vue";
import CardFooter from "@/components/cards/cardFooter.vue";
import TextareaInput from "@/components/inputs/textareaInput.vue";
import Toggle from "@/components/inputs/toggle.vue";
import { useDepartments } from "@/composables/departments";
import { SelectItem } from "@/types";
import SelectInput from "@/components/inputs/selectInput.vue";

const props = defineProps<{
  student: App.Data.Students.StudentData;
}>();

const emit = defineEmits<(e: "close") => void>();

const { departments, isLoading, error } = useDepartments();
const programs = ref<SelectItem[]>([{ id: 0, name: "Loading..." }]);

const originalDepartment = computed(() =>
  departments.value.find((department) => department.id === props.student.basic.departmentId),
);

const form = useForm({
  department: props.student.basic.departmentId,
  program: props.student.basic.programId,
  department_object: { id: props.student.basic.departmentId, name: "" } as SelectItem,
  program_object: { id: props.student.basic.programId, name: "" } as SelectItem,
  remark: "",
  has_mail: false,
  mail_title: "",
  mail_date: "",
});

const title = `Update Student's Department (${props.student.basic.registrationNumber})`;

const canNotUpdate = computed(() => props.student.basic.programId === form.program_object.id || form.processing);

watch(
  () => form.has_mail,
  () => {
    form.mail_title = "";
    form.mail_date = "";
    form.clearErrors();
  },
);

watch(
  () => isLoading.value,
  () => {
    if (!isLoading.value && departments.value.length > 1) {
      loadPrograms(originalDepartment.value);
    }
  },
);

const submit = () =>
  form
    .transform((data) => ({ ...data, program: data.program_object.id, department: data.department_object.id }))
    .patch(route("student.program.update", { student: props.student.basic.slug }), {
      onSuccess: () => emit("close"),
    });

const loadPrograms = (department) => {
  form.reset(["program_object", "program"]);
  if (programs.value !== null) {
    programs.value = department.programs.programs;
  }
};
</script>

<template>
  <BaseFormSection
    :header="title"
    description="Update student's department and submit">
    <p
      v-if="error"
      class="text-red-600">
      {{ error }}
    </p>

    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <div class="flex-1">
        <InputLabel
          for="department"
          value="Program" />

        <SelectInput
          v-if="!isLoading"
          id="department"
          v-model="form.department_object"
          :items="departments"
          :selected="student.basic.departmentId"
          @update:modelValue="loadPrograms" />

        <SelectInput
          v-else
          id="department_loading"
          :items="departments" />

        <InputError :message="form.errors.department" />
      </div>

      <div class="flex-1">
        <InputLabel
          for="program"
          value="Program" />

        <SelectInput
          v-if="!isLoading && programs.length > 1"
          id="program"
          v-model="form.program_object"
          :items="programs"
          :selected="student.basic.programId" />

        <SelectInput
          v-else
          id="program_loading"
          :items="programs" />

        <InputError :message="form.errors.program" />
      </div>

      <div class="">
        <InputLabel
          for="remark"
          value="Remark (state action performed)" />

        <TextareaInput
          id="remark"
          v-model="form.remark"
          required />

        <InputError :message="form.errors.remark" />
      </div>

      <div class="">
        <Toggle
          v-model="form.has_mail"
          label="Has mail" />
      </div>

      <template v-if="form.has_mail">
        <div class="">
          <InputLabel
            for="mail_title"
            value="Mail Title" />

          <TextareaInput
            id="mail_title"
            v-model="form.mail_title"
            autocomplete="mail_title"
            required />

          <InputError :message="form.errors.mail_title" />
        </div>

        <div class="mt-2">
          <InputLabel
            for="mail_date"
            value="Mail Date" />

          <TextInput
            id="mail_date"
            v-model="form.mail_date"
            autocomplete="mail_date"
            placeholder="YYYY-MM-DD"
            required
            type="text" />

          <InputError :message="form.errors.mail_date" />
        </div>
      </template>

      <CardFooter class="mt-6">
        <div class="mt-2 flex justify-end">
          <SecondaryButton @click="emit('close')">Cancel</SecondaryButton>

          <PrimaryButton
            :disabled="canNotUpdate"
            class="ms-3">
            Update
          </PrimaryButton>
        </div>
      </CardFooter>
    </form>
  </BaseFormSection>
</template>
