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
import { SelectItem } from "@/types";
import SelectInput from "@/components/inputs/selectInput.vue";
import { useStates } from "@/composables/states";
import FormGroup from "@/components/forms/formGroup.vue";

const props = defineProps<{
  student: App.Data.Students.StudentData;
}>();

const emit = defineEmits<(e: "close") => void>();

const { states, isLoading, error } = useStates();
const localGovernments = ref<SelectItem[]>([{ id: 0, name: "Loading..." }]);

const originalState = computed(() => states.value.find((state) => state.id === props.student.others.stateId));

const form = useForm({
  state: props.student.others.stateId,
  local_government: props.student.others.localGovernmentId,
  state_object: { id: props.student.others.stateId, name: "" } as SelectItem,
  local_government_object: { id: props.student.others.localGovernmentId, name: "" } as SelectItem,
  remark: "",
});

const title = `Update Student's State (${props.student.basic.registrationNumber})`;

const canNotUpdate = computed(
  () => props.student.others.localGovernmentId === form.local_government_object.id || form.processing,
);

watch(
  () => isLoading.value,
  () => {
    if (!isLoading.value && states.value.length > 1) {
      loadLocalGovernments(originalState.value);
    }
  },
);

const submit = () =>
  form
    .transform((data) => ({ ...data, local_government: data.local_government_object.id, state: data.state_object.id }))
    .patch(route("student.localGovernment.update", { student: props.student.basic.slug }), {
      onSuccess: () => emit("close"),
    });

const loadLocalGovernments = (state) => {
  form.reset(["local_government_object", "local_government"]);
  if (localGovernments.value !== null) {
    localGovernments.value = state.localGovernments.data;
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
      <FormGroup>
        <div class="flex-1">
          <InputLabel
            for="state"
            value="State" />

          <SelectInput
            v-if="!isLoading"
            id="state"
            v-model="form.state_object"
            :items="states"
            :selected="student.others.stateId"
            @update:modelValue="loadLocalGovernments" />

          <SelectInput
            v-else
            id="state_loading"
            :items="states" />

          <InputError :message="form.errors.state" />
        </div>

        <div class="flex-1">
          <InputLabel
            for="local_government"
            value="Local Government" />

          <SelectInput
            v-if="!isLoading && localGovernments.length > 1"
            id="local_government"
            v-model="form.local_government_object"
            :items="localGovernments"
            :selected="student.others.localGovernmentId" />

          <SelectInput
            v-else
            id="local_government_loading"
            :items="localGovernments" />

          <InputError :message="form.errors.local_government" />
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
