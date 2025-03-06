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
import { useEntryModes } from '@/composables/entryModes';

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
    <BaseFormSection
        :header="title"
        description="Correct student's Entry Mode and submit">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <div class="">
                <InputLabel
                    for="entry_mode"
                    value="Entry Mode" />

                <SelectInput
                    id="month"
                    v-model="form.entry_mode_object"
                    :items="modes"
                    :selected="student.others.entryMode"
                    class="mt-1 block w-full" />

                <InputError :message="form.errors.entry_mode" />
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
