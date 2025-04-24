<script lang="ts" setup>
import { FormGroup, FormSection } from '@/components/forms';
import { InputError, InputLabel, SelectInput, TextareaInput } from '@/components/inputs';
import { useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { SelectItem } from '@/types';
import { useStates } from '@/composables/states';
import { PrimaryButton, SecondaryButton } from '@/components/buttons';

const props = defineProps<{
    student: App.Data.Students.StudentData;
}>();

const emit = defineEmits<(e: 'close') => void>();

import StateData = App.Data.States.StateData;

const { states, isLoading, error } = useStates();
const localGovernments = ref<SelectItem[]>([{ id: 0, name: 'Loading...' }]);

const originalState = computed(() =>
    states.value.find((state: SelectItem) => state.id === props.student.others.stateId),
);

const form = useForm({
    state: props.student.others.stateId,
    local_government: props.student.others.localGovernmentId,
    state_object: { id: props.student.others.stateId, name: '' } as SelectItem,
    local_government_object: { id: props.student.others.localGovernmentId, name: '' } as SelectItem,
    remark: '',
});

const title = `Update Student's State (${props.student.basic.registrationNumber})`;

const canNotUpdate = computed(
    () => props.student.others.localGovernmentId === form.local_government_object.id || form.processing,
);

watch(
    () => isLoading.value,
    () => {
        if (!isLoading.value && states.value.length > 1) {
            loadLocalGovernments(originalState.value as StateData);
        }
    },
);

const submit = () =>
    form
        .transform((data) => ({
            ...data,
            local_government: data.local_government_object.id,
            state: data.state_object.id,
        }))
        .patch(route('student.localGovernment.update', { student: props.student.basic.slug }), {
            onSuccess: () => emit('close'),
        });

const loadLocalGovernments = (state: App.Data.States.StateData) => {
    form.reset('local_government_object', 'local_government');
    if (state.localGovernments !== null) {
        localGovernments.value = state.localGovernments.data;
    }
};
</script>

<template>
    <FormSection
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
                <div class="grid flex-1 gap-2">
                    <InputLabel for="state">State </InputLabel>

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

                <div class="grid flex-1 gap-2">
                    <InputLabel for="local_government">Local Government </InputLabel>

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

            <div class="grid gap-2">
                <InputLabel for="remark">Remark (state action performed) </InputLabel>

                <TextareaInput
                    id="remark"
                    v-model="form.remark"
                    required />

                <InputError :message="form.errors.remark" />
            </div>

            <div class="mt-2 flex justify-end gap-2">
                <SecondaryButton @click="emit('close')"> Cancel</SecondaryButton>

                <PrimaryButton :disabled="canNotUpdate"> Update</PrimaryButton>
            </div>
        </form>
    </FormSection>
</template>
