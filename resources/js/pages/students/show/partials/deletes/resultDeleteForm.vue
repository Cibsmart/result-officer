<script lang="ts" setup>
import BaseFormSection from "@/components/forms/baseFormSection.vue";
import TextInput from "@/components/inputs/textInput.vue";
import InputError from "@/components/inputs/inputError.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import { useForm } from "@inertiajs/vue3";
import SecondaryButton from "@/components/buttons/secondaryButton.vue";
import { computed } from "vue";
import CardFooter from "@/components/cards/cardFooter.vue";
import TextareaInput from "@/components/inputs/textareaInput.vue";
import DangerButton from "@/components/buttons/dangerButton.vue";

const props = defineProps<{
  student: App.Data.Students.StudentData;
  result: App.Data.Results.ResultData;
}>();

const emit = defineEmits<(e: "close") => void>();

const form = useForm({
  remark: "",
  mail_title: "",
  mail_date: "",
  password: "",
  result: props.result.id,
});

const title = `Delete Student's Result (${props.student.basic.registrationNumber})`;
const description = `${props.result.courseCode} - ${props.result.courseTitle} - ${props.result.totalScore} - ${props.result.grade}`;

const canNotUpdate = computed(() => form.processing);

const submit = () =>
  form.delete(
    route("student.registration.destroy", { student: props.student.basic.slug, registration: props.result.id }),
    { preserveScore: true, onSuccess: () => emit("close") },
  );
</script>

<template>
  <BaseFormSection
    :description="description"
    :header="title">
    <InputError :message="form.errors.result" />

    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
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
          autocomplete="off"
          placeholder="YYYY-MM-DD"
          required
          type="text" />

        <InputError :message="form.errors.mail_date" />
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

      <div class="mt-2">
        <InputLabel
          for="password"
          value="Password (for confirmation and signature)" />

        <TextInput
          id="password"
          v-model="form.password"
          autocomplete="off"
          placeholder="Password"
          required
          type="password" />

        <InputError :message="form.errors.password" />
      </div>

      <CardFooter class="mt-6">
        <div class="mt-2 flex justify-end">
          <SecondaryButton @click="emit('close')">Cancel</SecondaryButton>

          <DangerButton
            :disabled="canNotUpdate"
            class="ms-3">
            Delete
          </DangerButton>
        </div>
      </CardFooter>
    </form>
  </BaseFormSection>
</template>
