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
import { SelectItem } from "@/types";
import SelectInput from "@/components/inputs/selectInput.vue";

const props = defineProps<{
  student: App.Data.Students.StudentData;
}>();

const emit = defineEmits<(e: "close") => void>();

const levels = [
  { id: 0, name: "Select Level" },
  { id: 1, name: "100" },
  { id: 2, name: "200" },
];

const form = useForm({
  entry_level: props.student.others.entryLevel,
  entry_level_object: levels[0] as SelectItem,
  remark: "",
});

const title = `Update Student's Entry Level (${props.student.basic.registrationNumber})`;

const canNotUpdate = computed(
  () => props.student.others.entryLevel === form.entry_level_object.name || form.processing,
);

const submit = () =>
  form
    .transform((data) => ({ ...data, entry_level: data.entry_level_object.name }))
    .patch(route("student.entryLevel.update", { student: props.student.basic.slug }), {
      onSuccess: () => emit("close"),
    });

const selected = computed(() => {
  const level = props.student.others.entryLevel;
  return Number(level[0]);
});
</script>

<template>
  <BaseFormSection
    :header="title"
    description="Correct student's entry level and submit">
    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <div class="">
        <InputLabel
          for="entry_level"
          value="Entry Level" />

        <SelectInput
          id="month"
          v-model="form.entry_level_object"
          :items="levels"
          :selected="selected"
          class="mt-1 block w-full" />

        <InputError :message="form.errors.entry_level" />
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
