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
import LabelInput from "@/components/inputs/labelInput.vue";
import { useCreditUnits } from "@/composables/creditUnits";
import SelectInput from "@/components/inputs/selectInput.vue";

const props = defineProps<{
  student: App.Data.Students.StudentData;
  result: App.Data.Results.ResultData;
}>();

const emit = defineEmits<(e: "close") => void>();

const { units } = useCreditUnits();

const form = useForm({
  credit_unit: props.result.creditUnit,
  credit_unit_object: units[0],
  in_course: props.result.inCourseScore,
  exam: props.result.examScore,
  remark: "",
  has_mail: false,
  mail_title: "",
  mail_date: "",
  registration_id: props.result.id,
  result: "",
  password: "",
});

const title = `Update Student's Result (${props.student.basic.registrationNumber})`;
const oldName = computed(() => `${props.result.creditUnit}-${props.result.inCourseScore}-${props.result.examScore}`);
const newName = computed(() => `${form.credit_unit_object.id}-${form.in_course}-${form.exam}`);
const total = computed(() => `${form.in_course + form.exam}`);

const canNotUpdate = computed(() => oldName.value === newName.value || form.processing);

watch(
  () => form.has_mail,
  () => {
    form.mail_title = "";
    form.mail_date = "";
    form.clearErrors();
  },
);

const submit = () =>
  form
    .transform((data) => ({ ...data, credit_unit: data.credit_unit_object.id }))
    .patch(route("student.result.update", { student: props.student.basic.slug }), {
      onSuccess: () => emit("close"),
    });
</script>

<template>
  <BaseFormSection
    :header="title"
    description="Update student's result and submit">
    <InputError :message="form.errors.result" />

    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <FormGroup>
        <div class="flex-1">
          <InputLabel
            for="credit_unit"
            value="Credit Unit" />

          <SelectInput
            id="month"
            v-model="form.credit_unit_object"
            :items="units"
            :selected="result.creditUnit" />

          <InputError :message="form.errors.credit_unit" />
        </div>

        <div class="flex-1">
          <InputLabel
            for="in_course"
            value="In Course Score" />

          <TextInput
            id="in_course"
            v-model="form.in_course"
            autocomplete="off"
            max="50"
            min="0"
            required
            type="number" />

          <InputError :message="form.errors.in_course" />
        </div>

        <div class="flex-1">
          <InputLabel
            for="other_names"
            value="Exam Score" />

          <TextInput
            id="exam"
            v-model="form.exam"
            autocomplete="off"
            max="100"
            min="0"
            type="number" />

          <InputError :message="form.errors.exam" />
        </div>

        <div class="flex-1">
          <InputLabel value="Total Score" />

          <LabelInput>{{ total }}</LabelInput>
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
