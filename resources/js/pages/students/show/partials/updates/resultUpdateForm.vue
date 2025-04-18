<script lang="ts" setup>
import { FormGroup, FormSection } from '@/components/forms';
import { InputError, InputLabel, LabelInput, SelectInput, TextareaInput, TextInput, Toggle } from '@/components/inputs';
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { useCreditUnits } from '@/composables/creditUnits';
import { PrimaryButton, SecondaryButton } from '@/components/buttons';

const props = defineProps<{
    student: App.Data.Students.StudentData;
    result: App.Data.Results.ResultData;
}>();

const emit = defineEmits<(e: 'close') => void>();

const { units } = useCreditUnits();

const form = useForm({
    credit_unit: props.result.creditUnit,
    credit_unit_object: units[0],
    in_course: props.result.inCourseScore,
    in_course_2: props.result.inCourseScore2,
    exam: props.result.examScore,
    remark: '',
    has_mail: false,
    mail_title: '',
    mail_date: '',
    registration_id: props.result.id,
    result: '',
    password: '',
});

const title = `Update Student's Result (${props.student.basic.registrationNumber})`;
const oldValue = computed(
    () =>
        `${props.result.creditUnit}-${props.result.inCourseScore}-${props.result.inCourseScore2}-${props.result.examScore}`,
);
const newValue = computed(() => `${form.credit_unit_object.id}-${form.in_course}-${form.in_course_2}-${form.exam}`);
const total = computed(() => `${form.in_course + form.in_course_2 + form.exam}`);

const canNotUpdate = computed(() => oldValue.value === newValue.value || form.processing);

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
        .transform((data) => ({ ...data, credit_unit: data.credit_unit_object.id }))
        .patch(route('student.result.update', { student: props.student.basic.slug }), {
            onSuccess: () => emit('close'),
        });
</script>

<template>
    <FormSection
        :header="title"
        description="Update student's result and submit">
        <InputError :message="form.errors.result" />

        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <FormGroup>
                <div class="grid flex-1 gap-2">
                    <InputLabel for="in_course">In Course 1 Score</InputLabel>

                    <TextInput
                        id="in_course"
                        v-model="form.in_course"
                        autocomplete="off"
                        max="50"
                        min="0"
                        required
                        type="number" />

                    <InputError :message="form.errors.in_course" />
                </div>

                <div class="grid flex-1 gap-2">
                    <InputLabel for="in_course_2">In Course 2 Score</InputLabel>

                    <TextInput
                        id="in_course_2"
                        v-model="form.in_course_2"
                        autocomplete="off"
                        max="50"
                        min="0"
                        required
                        type="number" />

                    <InputError :message="form.errors.in_course_2" />
                </div>

                <div class="grid flex-1 gap-2">
                    <InputLabel for="other_names">Exam Score</InputLabel>

                    <TextInput
                        id="exam"
                        v-model="form.exam"
                        autocomplete="off"
                        max="100"
                        min="0"
                        type="number" />

                    <InputError :message="form.errors.exam" />
                </div>
            </FormGroup>

            <FormGroup>
                <div class="grid flex-1 gap-2">
                    <InputLabel for="credit_unit">Credit Unit</InputLabel>

                    <SelectInput
                        id="month"
                        v-model="form.credit_unit_object"
                        :items="units"
                        :selected="result.creditUnit" />

                    <InputError :message="form.errors.credit_unit" />
                </div>

                <div class="grid flex-1 gap-2">
                    <InputLabel for="total">Total Score</InputLabel>

                    <LabelInput
                        id="total"
                        v-model="total"
                        autocomplete="off" />
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

            <div class="flex justify-end gap-2">
                <SecondaryButton @click="emit('close')"> Cancel</SecondaryButton>

                <PrimaryButton :disabled="canNotUpdate"> Update</PrimaryButton>
            </div>
        </form>
    </FormSection>
</template>
