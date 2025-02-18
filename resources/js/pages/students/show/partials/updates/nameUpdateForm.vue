<script lang="ts" setup>
import BaseFormSection from "@/components/forms/baseFormSection.vue";
import TextInput from "@/components/inputs/textInput.vue";
import InputError from "@/components/inputs/inputError.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import { useForm } from "@inertiajs/vue3";
import SecondaryButton from "@/components/buttons/secondaryButton.vue";
import { computed, watch } from "vue";
import CardFooter from "@/components/cards/cardFooter.vue";
import TextareaInput from "@/components/inputs/textareaInput.vue";
import Toggle from "@/components/inputs/toggle.vue";
import FormGroup from "@/components/forms/formGroup.vue";

const props = defineProps<{
  student: App.Data.Students.StudentBasicData;
}>();

const emit = defineEmits<(e: "close") => void>();

const form = useForm({
  last_name: props.student.lastName,
  first_name: props.student.firstName,
  other_names: props.student.otherNames,
  remark: "",
  has_mail: false,
  mail_title: "",
  mail_date: "",
});

const title = `Update Student's Name (${props.student.registrationNumber})`;

const canNotUpdate = computed(
  () =>
    (props.student.lastName === form.last_name &&
      props.student.firstName === form.first_name &&
      props.student.otherNames === form.other_names) ||
    form.processing,
);

watch(
  () => form.has_mail,
  () => {
    form.mail_title = "";
    form.mail_date = "";
    form.clearErrors();
  },
);

const submit = () =>
  form.patch(route("student.name.update", { student: props.student.slug }), {
    onSuccess: () => emit("close"),
  });
</script>

<template>
  <BaseFormSection
    :header="title"
    description="Correct student's name and submit">
    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <div class="">
        <InputLabel
          for="last_name"
          value="Last Name" />

        <TextInput
          id="last_name"
          v-model="form.last_name"
          autocomplete="off"
          autofocus
          required
          type="text" />

        <InputError :message="form.errors.last_name" />
      </div>

      <FormGroup>
        <div class="flex-1">
          <InputLabel
            for="first_name"
            value="First Name" />

          <TextInput
            id="first_name"
            v-model="form.first_name"
            autocomplete="off"
            required
            type="text" />

          <InputError :message="form.errors.first_name" />
        </div>

        <div class="flex-1">
          <InputLabel
            for="other_names"
            value="Other Names" />

          <TextInput
            id="other_names"
            v-model="form.other_names"
            autocomplete="off"
            type="text" />

          <InputError :message="form.errors.other_names" />
        </div>
      </FormGroup>

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
