<script lang="ts" setup>
import { FormSection } from '@/components/forms';
import { InputError, InputLabel, SelectInput, TextareaInput, TextInput, Toggle } from '@/components/inputs';
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { EnumSelectItem } from '@/types';
import { useStudentStatues } from '@/composables/studentStatues';
import { PrimaryButton, SecondaryButton } from '@/components/buttons';

const props = defineProps<{
    student: App.Data.Students.StudentData;
}>();

const emit = defineEmits<(e: 'close') => void>();

const { statues } = useStudentStatues();

const form = useForm({
    status: props.student.basic.status,
    status_object: statues[0] as EnumSelectItem,
    remark: '',
    has_mail: false,
    mail_title: '',
    mail_date: '',
});

const title = `Update Student's Status (${props.student.basic.registrationNumber})`;

const canNotUpdate = computed(() => props.student.basic.status === form.status_object.id || form.processing);

watch(
    () => form.has_mail,
    () => {
        form.mail_title = '';
        form.mail_date = '';
        form.clearErrors();
    },
);

const submit = () =>
    form
        .transform((data) => ({ ...data, status: data.status_object.id }))
        .patch(route('student.status.update', { student: props.student.basic.slug }), {
            onSuccess: () => emit('close'),
        });
</script>

<template>
    <FormSection
        :header="title"
        description="Update student's status and submit">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <div class="grid gap-2">
                <InputLabel for="status">Status</InputLabel>

                <SelectInput
                    id="status"
                    v-model="form.status_object"
                    :items="statues"
                    :selected="student.basic.status" />

                <InputError :message="form.errors.status" />
            </div>

            <div class="grid gap-2">
                <InputLabel for="remark">Remark (state action performed)</InputLabel>

                <TextareaInput
                    id="remark"
                    v-model="form.remark"
                    required />

                <InputError :message="form.errors.remark" />
            </div>

            <div class="grid gap-2">
                <Toggle
                    v-model="form.has_mail"
                    label="Has mail" />
            </div>

            <template v-if="form.has_mail">
                <div class="grid gap-2">
                    <InputLabel for="mail_title">Mail Title</InputLabel>

                    <TextareaInput
                        id="mail_title"
                        v-model="form.mail_title"
                        autocomplete="mail_title"
                        required />

                    <InputError :message="form.errors.mail_title" />
                </div>

                <div class="grid gap-2">
                    <InputLabel for="mail_date">Mail Date</InputLabel>

                    <TextInput
                        id="mail_date"
                        v-model="form.mail_date"
                        autocomplete="mail_date"
                        placeholder="YYYY-MM-DD"
                        required />

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
