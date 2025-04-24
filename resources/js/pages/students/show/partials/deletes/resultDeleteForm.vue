<script lang="ts" setup>
import { InputError, InputLabel, TextareaInput, TextInput } from '@/components/inputs';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Card, CardContent, CardDescription, CardHeader } from '@/components/ui/card';
import { DangerButton, SecondaryButton } from '@/components/buttons';

const props = defineProps<{
    student: App.Data.Students.StudentData;
    result: App.Data.Results.ResultData;
}>();

const emit = defineEmits<(e: 'close') => void>();

const form = useForm({
    remark: '',
    mail_title: '',
    mail_date: '',
    password: '',
    result: props.result.id,
});

const title = `Delete Student's Result (${props.student.basic.registrationNumber})`;
const description = `${props.result.courseCode} - ${props.result.courseTitle} - ${props.result.totalScore} - ${props.result.grade}`;

const canNotUpdate = computed(() => form.processing);

const submit = () =>
    form.delete(
        route('student.registration.destroy', { student: props.student.basic.slug, registration: props.result.id }),
        { preserveScroll: true, onSuccess: () => emit('close') },
    );
</script>

<template>
    <Card>
        <CardHeader>{{ title }}</CardHeader>

        <CardDescription>{{ description }}</CardDescription>

        <CardContent>
            <InputError :message="form.errors.result" />

            <form
                class="mt-6 space-y-6"
                @submit.prevent="submit">
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
                        autocomplete="off"
                        placeholder="YYYY-MM-DD"
                        required
                        type="text" />

                    <InputError :message="form.errors.mail_date" />
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
                    <InputLabel for="password">Password (for confirmation and signature)</InputLabel>

                    <TextInput
                        id="password"
                        v-model="form.password"
                        autocomplete="off"
                        placeholder="Password"
                        required
                        type="password" />

                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex justify-end">
                    <SecondaryButton @click="emit('close')"> Cancel</SecondaryButton>

                    <DangerButton :disabled="canNotUpdate"> Delete</DangerButton>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
