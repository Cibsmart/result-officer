<script lang="ts" setup>
import { FormSection } from '@/components/forms';
import { InputError, InputLabel, SelectInput, TextareaInput } from '@/components/inputs';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useSessions } from '@/composables/sessions';
import { SelectItem } from '@/types';
import { PrimaryButton, SecondaryButton } from '@/components/buttons';

const props = defineProps<{
    student: App.Data.Students.StudentData;
}>();

const emit = defineEmits<(e: 'close') => void>();

const { sessions, isLoading } = useSessions();

const form = useForm({
    entry_session: props.student.others.entrySession,
    entry_session_object: { id: props.student.others.entrySessionId, name: '' } as SelectItem,
    remark: '',
});

const title = `Update Student's Registration Number (${props.student.basic.registrationNumber})`;

const canNotUpdate = computed(
    () => props.student.others.entrySessionId === form.entry_session_object.id || form.processing,
);

const submit = () =>
    form
        .transform((data) => ({ ...data, entry_session: data.entry_session_object.id }))
        .patch(route('student.entrySession.update', { student: props.student.basic.slug }), {
            onSuccess: () => emit('close'),
        });
</script>

<template>
    <FormSection
        :header="title"
        description="Correct student's registration number and submit">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <div class="grid gap-2">
                <InputLabel for="entry_session">Entry Session </InputLabel>

                <SelectInput
                    v-if="!isLoading"
                    id="entry_session"
                    v-model="form.entry_session_object"
                    :items="sessions"
                    :selected="student.others.entrySessionId" />

                <SelectInput
                    v-else
                    id="entry_session_loading"
                    :items="sessions" />

                <InputError :message="form.errors.entry_session" />
            </div>

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
