<script lang="ts" setup>
import BaseFormSection from '@/components/forms/baseFormSection.vue';
import InputError from '@/components/inputs/inputError.vue';
import InputLabel from '@/components/inputs/inputLabel.vue';
import PrimaryButton from '@/components/buttons/primaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import SecondaryButton from '@/components/buttons/secondaryButton.vue';
import { computed } from 'vue';
import CardFooter from '@/components/cards/cardFooter.vue';
import TextareaInput from '@/components/inputs/textareaInput.vue';
import SelectInput from '@/components/inputs/selectInput.vue';
import { useSessions } from '@/composables/sessions';
import { SelectItem } from '@/types';

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
    <BaseFormSection
        :header="title"
        description="Correct student's registration number and submit">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <div class="">
                <InputLabel
                    for="entry_session"
                    value="Entry Session" />

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
