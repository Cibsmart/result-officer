<script lang="ts" setup>
import { FormSection } from '@/components/forms';
import InputError from '@/components/inputs/inputError.vue';
import InputLabel from '@/components/inputs/inputLabel.vue';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import TextareaInput from '@/components/inputs/textareaInput.vue';
import SelectInput from '@/components/inputs/selectInput.vue';
import { useLevels } from '@/composables/levels';
import { PrimaryButton, SecondaryButton } from '@/components/buttons';

const props = defineProps<{
    student: App.Data.Students.StudentData;
}>();

const emit = defineEmits<(e: 'close') => void>();

const { levels } = useLevels();

const form = useForm({
    entry_level: props.student.others.entryLevel,
    entry_level_object: levels[0],
    remark: '',
});

const title = `Update Student's Entry Level (${props.student.basic.registrationNumber})`;

const canNotUpdate = computed(
    () => props.student.others.entryLevel === form.entry_level_object.name || form.processing,
);

const submit = () =>
    form
        .transform((data) => ({ ...data, entry_level: data.entry_level_object.name }))
        .patch(route('student.entryLevel.update', { student: props.student.basic.slug }), {
            onSuccess: () => emit('close'),
        });
</script>

<template>
    <FormSection
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
                    :selected="student.others.entryLevel"
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

            <div class="mt-2 flex justify-end gap-2">
                <SecondaryButton @click="emit('close')"> Cancel</SecondaryButton>

                <PrimaryButton :disabled="canNotUpdate"> Update</PrimaryButton>
            </div>
        </form>
    </FormSection>
</template>
