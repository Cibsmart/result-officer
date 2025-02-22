<script lang="ts" setup>
import BaseFormSection from "@/components/forms/baseFormSection.vue";
import TextInput from "@/components/inputs/textInput.vue";
import InputError from "@/components/inputs/inputError.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import { useForm, router, usePage } from "@inertiajs/vue3";
import SecondaryButton from "@/components/buttons/secondaryButton.vue";
import { computed, watch, onMounted, ref } from "vue";
import CardFooter from "@/components/cards/cardFooter.vue";
import TextareaInput from "@/components/inputs/textareaInput.vue";
import Toggle from "@/components/inputs/toggle.vue";
import { EnumSelectItem } from "@/types";
import SelectInput from "@/components/inputs/selectInput.vue";

const props = defineProps<{
  student: App.Data.Students.StudentData;
}>();

const emit = defineEmits<(e: "close") => void>();

const page = usePage();

const statues = ref<EnumSelectItem[]>();

const form = useForm({
  status: props.student.basic.status,
  status_object: null as EnumSelectItem,
  remark: "",
  has_mail: false,
  mail_title: "",
  mail_date: "",
});

const title = `Update Student's Status (${props.student.basic.registrationNumber})`;

const canNotUpdate = computed(() => props.student.basic.status === form.status_object?.id || form.processing);

watch(
  () => form.has_mail,
  () => {
    form.mail_title = "";
    form.mail_date = "";
    form.clearErrors();
  },
);

onMounted(() => loadStatues());

const submit = () =>
  form
    .transform((data) => ({ ...data, status: data.status_object ? data.status_object.id : data.status }))
    .patch(route("student.status.update", { student: props.student.basic.slug }), {
      onSuccess: () => emit("close"),
    });

const loadStatues = () => {
  router.reload({
    only: ["statues"],
    onSuccess: () => {
      statues.value = page.props.statues.data as EnumSelectItem[];
    },
  });
};
</script>

<template>
  <BaseFormSection
    :header="title"
    description="Update student's status and submit">
    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <div class="">
        <InputLabel
          for="status"
          value="Status" />

        <SelectInput
          v-if="statues"
          id="status"
          v-model="form.status_object"
          :items="statues"
          :selected="student.basic.status" />

        <InputError :message="form.errors.status" />
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
