<script lang="ts" setup>
import { FormSection } from '@/components/forms';
import { InputError, InputLabel, TextareaInput, TextInput } from '@/components/inputs';
import { PrimaryButton, SecondaryButton } from '@/components/buttons';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    student: App.Data.Students.StudentData;
}>();

const emit = defineEmits<(e: 'close') => void>();

const form = useForm({
    date_of_birth: props.student.basic.birthDate,
    remark: '',
});

const title = `Update Student's Date of Birth (${props.student.basic.registrationNumber})`;

const canNotUpdate = computed(() => props.student.basic.birthDate === form.date_of_birth || form.processing);

const submit = () =>
    form.patch(route('student.birthDate.update', { student: props.student.basic.slug }), {
        onSuccess: () => emit('close'),
    });
</script>

<template>
    <FormSection
        :header="title"
        description="Update student's date of birth and submit">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <div class="grid gap-2">
                <InputLabel for="date_of_birth">Date of Birth</InputLabel>

                <TextInput
                    id="date_of_birth"
                    v-model="form.date_of_birth"
                    autocomplete="off"
                    autofocus
                    placeholder="YYYY-MM-DD"
                    required
                    type="text" />

                <InputError :message="form.errors.date_of_birth" />
            </div>

            <div class="grid gap-2">
                <InputLabel for="remark">Remark (state action performed)</InputLabel>

                <TextareaInput
                    id="remark"
                    v-model="form.remark"
                    required />

                <InputError :message="form.errors.remark" />
            </div>

            <div class="flex justify-end gap-2">
                <SecondaryButton @click="emit('close')">Cancel</SecondaryButton>

                <PrimaryButton :disabled="canNotUpdate"> Update</PrimaryButton>
            </div>
        </form>
    </FormSection>
</template>
