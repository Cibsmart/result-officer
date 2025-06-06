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
    phone_number: props.student.others.phoneNumber,
    remark: '',
});

const title = `Update Student's Phone Number (${props.student.basic.registrationNumber})`;

const canNotUpdate = computed(() => props.student.others.phoneNumber === form.phone_number || form.processing);

const submit = () =>
    form.patch(route('student.phoneNumber.update', { student: props.student.basic.slug }), {
        onSuccess: () => emit('close'),
    });
</script>

<template>
    <FormSection
        :header="title"
        description="Update student's phone number and submit">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <div class="grid gap-2">
                <InputLabel for="phone_number">Phone Number</InputLabel>

                <TextInput
                    id="phone_number"
                    v-model="form.phone_number"
                    autocomplete="off"
                    autofocus
                    required
                    type="text" />

                <InputError :message="form.errors.phone_number" />
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
