<script lang="ts" setup>
import { FormSection } from '@/components/forms';
import { InputError, InputLabel, SelectInput, TextareaInput } from '@/components/inputs';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useEntryModes } from '@/composables/entryModes';
import { PrimaryButton, SecondaryButton } from '@/components/buttons';

const props = defineProps<{
    student: App.Data.Students.StudentData;
}>();

const emit = defineEmits<(e: 'close') => void>();

const { modes } = useEntryModes();

const form = useForm({
    entry_mode: props.student.others.entryMode,
    entry_mode_object: modes[0],
    remark: '',
});

const title = `Update Student's Entry Mode (${props.student.basic.registrationNumber})`;

const canNotUpdate = computed(() => props.student.others.entryMode === form.entry_mode_object.id || form.processing);

const submit = () =>
    form
        .transform((data) => ({ ...data, entry_mode: data.entry_mode_object.id }))
        .patch(route('student.entryMode.update', { student: props.student.basic.slug }), {
            onSuccess: () => emit('close'),
        });
</script>

<template>
    <FormSection
        :header="title"
        description="Correct student's Entry Mode and submit">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <div class="grid gap-2">
                <InputLabel for="entry_mode">Entry Mode</InputLabel>

                <SelectInput
                    id="month"
                    v-model="form.entry_mode_object"
                    :items="modes"
                    :selected="student.others.entryMode" />

                <InputError :message="form.errors.entry_mode" />
            </div>

            <div class="grid gap-2">
                <InputLabel for="remark">Remark (state action performed)</InputLabel>

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
