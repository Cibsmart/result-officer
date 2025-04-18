<script lang="ts" setup>
import { FormSection } from '@/components/forms';
import TextInput from '@/components/inputs/TextInput.vue';
import InputError from '@/components/inputs/InputError.vue';
import InputLabel from '@/components/inputs/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import TextareaInput from '@/components/inputs/TextareaInput.vue';
import Toggle from '@/components/inputs/toggle.vue';
import { PrimaryButton, SecondaryButton } from '@/components/buttons';

const props = defineProps<{
    student: App.Data.Students.StudentData;
}>();

const emit = defineEmits<(e: 'close') => void>();

const form = useForm({
    registration_number: props.student.basic.registrationNumber,
    remark: '',
    has_mail: false,
    mail_title: '',
    mail_date: '',
});

const title = `Update Student's Registration Number (${props.student.basic.registrationNumber})`;

const canNotUpdate = computed(
    () => props.student.basic.registrationNumber === form.registration_number || form.processing,
);

watch(
    () => form.has_mail,
    () => {
        form.mail_title = '';
        form.mail_date = '';
        form.clearErrors();
    },
);

const submit = () =>
    form.patch(route('student.registrationNumber.update', { student: props.student.basic.slug }), {
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
            <div class="">
                <InputLabel
                    for="registration_number"
                    value="Registration Number" />

                <TextInput
                    id="registration_number"
                    v-model="form.registration_number"
                    autocomplete="off"
                    autofocus
                    required
                    type="text" />

                <InputError :message="form.errors.registration_number" />
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

            <div class="">
                <Toggle
                    v-model="form.has_mail"
                    label="Has mail" />
            </div>

            <template v-if="form.has_mail">
                <div class="">
                    <InputLabel
                        for="mail_title"
                        value="Mail Title" />

                    <TextareaInput
                        id="mail_title"
                        v-model="form.mail_title"
                        autocomplete="mail_title"
                        required />

                    <InputError :message="form.errors.mail_title" />
                </div>

                <div class="mt-2">
                    <InputLabel
                        for="mail_date"
                        value="Mail Date" />

                    <TextInput
                        id="mail_date"
                        v-model="form.mail_date"
                        autocomplete="mail_date"
                        placeholder="YYYY-MM-DD"
                        required
                        type="text" />

                    <InputError :message="form.errors.mail_date" />
                </div>
            </template>

            <div class="flex justify-end gap-2">
                <SecondaryButton @click="emit('close')"> Cancel</SecondaryButton>

                <PrimaryButton :disabled="canNotUpdate"> Update</PrimaryButton>
            </div>
        </form>
    </FormSection>
</template>
