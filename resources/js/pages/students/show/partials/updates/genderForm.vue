<script lang="ts" setup>
import BaseFormSection from "@/components/forms/baseFormSection.vue";
import InputError from "@/components/inputs/inputError.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import { useForm } from "@inertiajs/vue3";
import SecondaryButton from "@/components/buttons/secondaryButton.vue";
import { computed } from "vue";
import CardFooter from "@/components/cards/cardFooter.vue";
import TextareaInput from "@/components/inputs/textareaInput.vue";
import SelectInput from "@/components/inputs/selectInput.vue";
import { useGenders } from "@/composables/genders";

const props = defineProps<{
  student: App.Data.Students.StudentData;
}>();

const emit = defineEmits<(e: "close") => void>();

const { genders } = useGenders();

const form = useForm({
  gender: props.student.basic.gender,
  gender_object: genders["0"],
  remark: "",
});

const title = `Update Student's Gender (${props.student.basic.registrationNumber})`;

const canNotUpdate = computed(() => props.student.basic.gender === form.gender_object.id || form.processing);

const submit = () =>
  form
    .transform((data) => ({ ...data, gender: data.gender_object.id }))
    .patch(route("student.gender.update", { student: props.student.basic.slug }), {
      onSuccess: () => emit("close"),
    });

const selected = computed(() => {
  const gender = props.student.basic.gender;
  return gender === "U" ? "0" : props.student.basic.gender;
});
</script>

<template>
  <BaseFormSection
    :header="title"
    description="Update student's gender and submit">
    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <div class="">
        <InputLabel
          for="gender"
          value="Gender" />

        <SelectInput
          id="gender"
          v-model="form.gender_object"
          :items="genders"
          :selected="selected" />

        <InputError :message="form.errors.gender" />
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
