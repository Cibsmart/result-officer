<script lang="ts" setup>
import { FormGroup, FormSection } from '@/components/forms';
import { InputError, InputLabel, TextareaInput, TextInput } from '@/components/inputs';
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import Toggle from '@/components/inputs/Toggle.vue';
import { PrimaryButton, SecondaryButton } from '@/components/buttons';

const props = defineProps<{
    student: App.Data.Students.StudentData;
}>();

const emit = defineEmits<(e: 'close') => void>();

const form = useForm({
    last_name: props.student.basic.lastName,
    first_name: props.student.basic.firstName,
    other_names: props.student.basic.otherNames,
    remark: '',
    has_mail: false,
    mail_title: '',
    mail_date: '',
    name: '',
});

const title = `Update Student's Name (${props.student.basic.registrationNumber})`;
const oldName = computed(
    () =>
        `${props.student.basic.lastName.trim()}, ${props.student.basic.firstName.trim()} ${props.student.basic.otherNames.trim()}`,
);
const newName = computed(() => `${form.last_name.trim()}, ${form.first_name.trim()} ${form.other_names.trim()}`);

const canNotUpdate = computed(() => oldName.value === newName.value || form.processing);

watch(
    () => form.has_mail,
    () => {
        form.mail_title = '';
        form.mail_date = '';
        form.clearErrors();
    },
);

const submit = () =>
    form.patch(route('student.name.update', { student: props.student.basic.slug }), {
        onSuccess: () => emit('close'),
    });
</script>

<template>
    <FormSection
        :header="title"
        description="Correct student's name and submit">
        <InputError :message="form.errors.name" />

        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <div class="grid gap-2">
                <InputLabel for="last_name">Last Name</InputLabel>

                <TextInput
                    id="last_name"
                    v-model="form.last_name"
                    autocomplete="off"
                    autofocus
                    class="uppercase"
                    required
                    type="text" />

                <InputError :message="form.errors.last_name" />
            </div>

            <FormGroup>
                <div class="grid flex-1 gap-2">
                    <InputLabel for="first_name">First Name</InputLabel>

                    <TextInput
                        id="first_name"
                        v-model="form.first_name"
                        autocomplete="off"
                        class="uppercase"
                        required
                        type="text" />

                    <InputError :message="form.errors.first_name" />
                </div>

                <div class="grid flex-1 gap-2">
                    <InputLabel for="other_names">Other Names</InputLabel>

                    <TextInput
                        id="other_names"
                        v-model="form.other_names"
                        autocomplete="off"
                        class="uppercase"
                        type="text" />

                    <InputError :message="form.errors.other_names" />
                </div>
            </FormGroup>

            <div class="grid gap-2">
                <InputLabel for="remark">Remark (state action performed)</InputLabel>

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
                        required
                        type="text" />

                    <InputError :message="form.errors.mail_date" />
                </div>
            </template>

            <div class="mt-2 flex justify-end gap-2">
                <SecondaryButton @click="emit('close')"> Cancel</SecondaryButton>

                <PrimaryButton :disabled="canNotUpdate"> Update</PrimaryButton>
            </div>
        </form>
    </FormSection>
</template>
