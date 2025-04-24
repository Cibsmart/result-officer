<script lang="ts" setup>
import { FormSection } from '@/components/forms';
import { InputError, InputLabel, SelectInput, TextareaInput, TextInput, Toggle } from '@/components/inputs';
import { useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { useDepartments } from '@/composables/departments';
import { SelectItem } from '@/types';
import { PrimaryButton, SecondaryButton } from '@/components/buttons';

const props = defineProps<{
    student: App.Data.Students.StudentData;
}>();

const emit = defineEmits<(e: 'close') => void>();

import DepartmentData = App.Data.Department.DepartmentData;

const { departments, isLoading, error } = useDepartments();
const programs = ref<SelectItem[]>([{ id: 0, name: 'Loading...' }]);

const originalDepartment = computed(() =>
    departments.value.find((department: SelectItem) => department.id === props.student.basic.departmentId),
);

const form = useForm({
    department: props.student.basic.departmentId,
    program: props.student.basic.programId,
    department_object: { id: props.student.basic.departmentId, name: '' } as SelectItem,
    program_object: { id: props.student.basic.programId, name: '' } as SelectItem,
    remark: '',
    has_mail: false,
    mail_title: '',
    mail_date: '',
});

const title = `Update Student's Department (${props.student.basic.registrationNumber})`;

const canNotUpdate = computed(() => props.student.basic.programId === form.program_object.id || form.processing);

watch(
    () => form.has_mail,
    () => {
        form.mail_title = '';
        form.mail_date = '';
        form.clearErrors();
    },
);

watch(
    () => isLoading.value,
    () => {
        if (!isLoading.value && departments.value.length > 1) {
            loadPrograms(originalDepartment.value as DepartmentData);
        }
    },
);

const submit = () =>
    form
        .transform((data) => ({ ...data, program: data.program_object.id, department: data.department_object.id }))
        .patch(route('student.program.update', { student: props.student.basic.slug }), {
            onSuccess: () => emit('close'),
        });

const loadPrograms = (department: App.Data.Department.DepartmentData) => {
    form.reset('program_object', 'program');
    if (department.programs !== null) {
        programs.value = department.programs.programs;
    }
};
</script>

<template>
    <FormSection
        :header="title"
        description="Update student's department and submit">
        <p
            v-if="error"
            class="text-red-600">
            {{ error }}
        </p>

        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <div class="grid flex-1 gap-2">
                <InputLabel for="department">Program</InputLabel>

                <SelectInput
                    v-if="!isLoading"
                    id="department"
                    v-model="form.department_object"
                    :items="departments"
                    :selected="student.basic.departmentId"
                    @update:modelValue="loadPrograms" />

                <SelectInput
                    v-else
                    id="department_loading"
                    :items="departments" />

                <InputError :message="form.errors.department" />
            </div>

            <div class="grid flex-1 gap-2">
                <InputLabel for="program">Program</InputLabel>

                <SelectInput
                    v-if="!isLoading && programs.length > 1"
                    id="program"
                    v-model="form.program_object"
                    :items="programs"
                    :selected="student.basic.programId" />

                <SelectInput
                    v-else
                    id="program_loading"
                    :items="programs" />

                <InputError :message="form.errors.program" />
            </div>

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
