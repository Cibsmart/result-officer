<script lang="ts" setup>
import BaseFormSection from "@/components/forms/baseFormSection.vue";
import TextInput from "@/components/inputs/textInput.vue";
import InputError from "@/components/inputs/inputError.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import { useForm } from "@inertiajs/vue3";
import SecondaryButton from "@/components/buttons/secondaryButton.vue";
import { computed } from "vue";
import CardFooter from "@/components/cards/cardFooter.vue";
import Card from "@/components/cards/card.vue";
import TextareaInput from "@/components/inputs/textareaInput.vue";
import Checkbox from "@/components/inputs/checkbox.vue";
import Toggle from "@/components/inputs/toggle.vue";

const props = defineProps<{
  student: App.Data.Students.StudentBasicData;
}>();

const emit = defineEmits<(e: "close") => void>();

const form = useForm({
  registration_number: props.student.registrationNumber,
  has_mail: false,
  mail_title: "",
  mail_date: "",
});

const title = `Update Student's Registration Number (${props.student.registrationNumber})`;

const canNotUpdate = computed(() => props.student.registrationNumber === form.registration_number || form.processing);

const submit = () =>
  form.patch(route("student.registrationNumber.update", { student: props.student.slug }), {
    onSuccess: () => emit("close"),
  });
</script>

<template>
  <BaseFormSection
    :header="title"
    description="Correct student's registration number and submit">
    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <div class="">
        <InputLabel
          for="registration_number"
          value="Registration Number" />

        <TextInput
          id="registration_number"
          v-model="form.registration_number"
          autocomplete="off"
          autofocus
          required
          type="text" />

        <InputError :message="form.errors.registration_number" />
      </div>

      <div class="">
        <Toggle
          v-model="form.has_mail"
          label="Has mail" />
      </div>

      <Card>
        <div class="">
          <InputLabel
            for="mail_title"
            value="Mail Title" />

          <TextareaInput
            id="mail_title"
            v-model="form.mail_title"
            autocomplete="off"
            autofocus
            required
            type="text" />

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
            autofocus
            required
            type="text" />

          <InputError :message="form.errors.mail_date" />
        </div>
      </Card>
    </form>

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
  </BaseFormSection>
</template>
