<script lang="ts" setup>
import SelectInput from "@/components/inputs/selectInput.vue";
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import InputError from "@/components/inputs/inputError.vue";
import BaseFormSection from "@/components/forms/baseFormSection.vue";
import SecondaryButton from "@/components/buttons/secondaryButton.vue";
import FormGroup from "@/components/forms/formGroup.vue";
import { useForm } from "@inertiajs/vue3";
import { useMonths } from "@/composables/months";
import { useYears } from "@/composables/year";
import { useExamOfficers } from "@/composables/examOfficers";

const props = defineProps<{
  student: App.Data.Graduands.GraduandData;
}>();

const emit = defineEmits<(e: "close") => void>();

const form = useForm({
  year: "",
  month: "",
  exam_officer: "",
  year_object: { id: "" },
  month_object: { id: "" },
  exam_officer_object: { id: "" },
});

const clearStudent = () => {
  form
    .transform((data) => ({
      ...data,
      year: data.year_object.id,
      month: data.month_object.id,
      exam_officer: data.exam_officer_object.id,
    }))
    .post(route("students.clearance.store", { student: props.student.slug }), {
      preserveScroll: true,
      onSuccess: () => emit("close"),
    });
};

const { months } = useMonths();
const { years } = useYears();
const { officers, isLoading } = useExamOfficers();
</script>

<template>
  <BaseFormSection
    description="Select Clearance Batch and Exam Officer"
    header="Clearance Confirmation">
    <form class="mt-6 space-y-6">
      <FormGroup>
        <div class="flex w-full items-start space-x-4">
          <div class="flex-1">
            <InputLabel
              for="year"
              value="Year" />

            <SelectInput
              id="year"
              v-model="form.year_object"
              :items="years"
              class="mt-1 block w-full" />

            <InputError
              :message="form.errors.year"
              class="mt-2" />
          </div>
        </div>

        <div class="flex w-full items-start space-x-4">
          <div class="flex-1">
            <InputLabel
              for="month"
              value="Month" />

            <SelectInput
              id="month"
              v-model="form.month_object"
              :items="months"
              class="mt-1 block w-full" />

            <InputError
              :message="form.errors.month"
              class="mt-2" />
          </div>
        </div>
      </FormGroup>

      <div class="flex w-full items-start space-x-4">
        <div class="flex-1">
          <InputLabel
            for="exam_officer"
            value="Exam Officer" />

          <SelectInput
            v-if="!isLoading"
            id="exam_officer"
            v-model="form.exam_officer_object"
            :items="officers" />

          <SelectInput
            v-else
            id="exam_officer_loading"
            :items="officers" />

          <InputError
            :message="form.errors.exam_officer"
            class="mt-2" />
        </div>
      </div>
    </form>

    <p class="mt-4 text-base text-red-600">
      You are confirming that
      <span class="font-bold">{{ student.name }}</span>
      with Registration Number
      <span class="font-bold">{{ student.registrationNumber }}</span>
      has satisfy
      <span class="font-bold">ALL</span> academic requirement for graduation.
    </p>

    <div class="mt-6 flex justify-end">
      <SecondaryButton @click="emit('close')"> Cancel</SecondaryButton>

      <PrimaryButton
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        class="ms-3"
        @click="clearStudent">
        Clear Student
      </PrimaryButton>
    </div>
  </BaseFormSection>
</template>
