<script lang="ts" setup>
import { FormSection } from '@/components/forms';
import { InputError, InputLabel, TextareaInput, TextInput } from '@/components/inputs';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { PrimaryButton, SecondaryButton } from '@/components/buttons';

const props = defineProps<{
    student: App.Data.Students.StudentData;
}>();

const emit = defineEmits<(e: 'close') => void>();

const form = useForm({
    jamb_registration_number: props.student.others.jambRegistrationNumber,
    remark: '',
});

const title = `Update Student's JAMB Registration Number (${props.student.basic.registrationNumber})`;

const canNotUpdate = computed(
    () => props.student.others.jambRegistrationNumber === form.jamb_registration_number || form.processing,
);

const submit = () =>
    form.patch(route('student.jambRegistrationNumber.update', { student: props.student.basic.slug }), {
        onSuccess: () => emit('close'),
    });
</script>

<template>
    <FormSection
        :header="title"
        description="Update student's JAMB registration number and submit">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <div class="">
                <InputLabel for="jamb_registration_number">Jamb Registration Number </InputLabel>

                <TextInput
                    id="jamb_registration_number"
                    v-model="form.jamb_registration_number"
                    autocomplete="off"
                    autofocus
                    required
                    type="text" />

                <InputError :message="form.errors.jamb_registration_number" />
            </div>

            <div class="">
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
