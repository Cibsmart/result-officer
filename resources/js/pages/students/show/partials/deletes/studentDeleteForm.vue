<script lang="ts" setup>
import { FormSection } from '@/components/forms';
import TextInput from '@/components/inputs/textInput.vue';
import InputError from '@/components/inputs/inputError.vue';
import InputLabel from '@/components/inputs/inputLabel.vue';
import { useForm } from '@inertiajs/vue3';
import { DangerButton, SecondaryButton } from '@/components/buttons';
import { computed, watch } from 'vue';
import TextareaInput from '@/components/inputs/textareaInput.vue';
import Toggle from '@/components/inputs/toggle.vue';

const props = defineProps<{
    student: App.Data.Students.StudentBasicData;
}>();

const emit = defineEmits<(e: 'close') => void>();

const form = useForm({
    remark: '',
    has_mail: false,
    mail_title: '',
    mail_date: '',
    password: '',
    student: props.student.id,
});

watch(
    () => form.has_mail,
    () => {
        form.mail_title = '';
        form.mail_date = '';
        form.clearErrors();
    },
);

const title = `Delete Student (${props.student.registrationNumber})`;
const description = `${props.student.name} with Registration Number ${props.student.registrationNumber} in ${props.student.departmentProgram} Department`;

const canNotUpdate = computed(() => form.processing);

const submit = () =>
    form.delete(route('student.destroy', { student: props.student.slug }), {
        preserveScroll: true,
        onSuccess: () => emit('close'),
    });
</script>

<template>
    <FormSection
        :description="description"
        :header="title">
        <InputError :message="form.errors.student" />

        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
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
                        autocomplete="off"
                        placeholder="YYYY-MM-DD"
                        required
                        type="text" />

                    <InputError :message="form.errors.mail_date" />
                </div>
            </template>

            <div class="mt-2">
                <InputLabel
                    for="password"
                    value="Password (for confirmation and signature)" />

                <TextInput
                    id="password"
                    v-model="form.password"
                    autocomplete="off"
                    placeholder="Password"
                    required
                    type="password" />

                <InputError :message="form.errors.password" />
            </div>

            <div class="mt-2 flex justify-end">
                <SecondaryButton @click="emit('close')">Cancel</SecondaryButton>

                <DangerButton :disabled="canNotUpdate"> Delete</DangerButton>
            </div>
        </form>
    </FormSection>
</template>
