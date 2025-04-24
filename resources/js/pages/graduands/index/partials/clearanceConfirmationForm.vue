<script lang="ts" setup>
import SelectInput from '@/components/inputs/SelectInput.vue';
import { InputError, InputLabel } from '@/components/inputs';
import { FormGroup, FormSection } from '@/components/forms';
import { useForm } from '@inertiajs/vue3';
import { useMonths } from '@/composables/months';
import { useYears } from '@/composables/year';
import { useExamOfficers } from '@/composables/examOfficers';
import { LoaderCircle } from 'lucide-vue-next';
import { PrimaryButton, SecondaryButton } from '@/components/buttons';

const props = defineProps<{
    student: App.Data.Graduands.GraduandData;
}>();

const emit = defineEmits<(e: 'close') => void>();

const form = useForm({
    year: '',
    month: '',
    exam_officer: '',
    year_object: { id: '' },
    month_object: { id: '' },
    exam_officer_object: { id: '' },
});

const clearStudent = () => {
    form.transform((data) => ({
        ...data,
        year: data.year_object.id,
        month: data.month_object.id,
        exam_officer: data.exam_officer_object.id,
    })).post(route('students.clearance.store', { student: props.student.slug }), {
        preserveScroll: true,
        onSuccess: () => emit('close'),
    });
};

const { months } = useMonths();
const { years } = useYears();
const { officers, isLoading } = useExamOfficers();
</script>

<template>
    <FormSection
        description="Select Clearance Batch and Exam Officer"
        header="Clearance Confirmation">
        <form class="mt-6 space-y-6">
            <FormGroup>
                <div class="flex w-full items-start space-x-4">
                    <div class="flex-1">
                        <div class="grid gap-2">
                            <InputLabel for="year">Year</InputLabel>

                            <SelectInput
                                id="year"
                                v-model="form.year_object"
                                :items="years" />

                            <InputError :message="form.errors.year" />
                        </div>
                    </div>
                </div>

                <div class="flex w-full items-start space-x-4">
                    <div class="flex-1">
                        <div class="grid gap-2">
                            <InputLabel for="month">Month</InputLabel>

                            <SelectInput
                                id="month"
                                v-model="form.month_object"
                                :items="months" />

                            <InputError :message="form.errors.month" />
                        </div>
                    </div>
                </div>
            </FormGroup>

            <div class="flex w-full items-start space-x-4">
                <div class="flex-1">
                    <div class="grid gap-2">
                        <InputLabel for="exam_officer">Exam Officer</InputLabel>

                        <SelectInput
                            v-if="!isLoading"
                            id="exam_officer"
                            v-model="form.exam_officer_object"
                            :items="officers" />

                        <SelectInput
                            v-else
                            id="exam_officer_loading"
                            :items="officers" />

                        <InputError :message="form.errors.exam_officer" />
                    </div>
                </div>
            </div>
        </form>

        <p class="mt-4 text-base text-red-600">
            You are confirming that
            <span class="font-bold">{{ student.name }}</span>
            with Registration Number
            <span class="font-bold">{{ student.registrationNumber }}</span>
            has satisfy
            <span class="font-bold">ALL</span> academic requirement for graduation.
        </p>

        <div class="flex justify-end gap-2">
            <SecondaryButton @click="emit('close')"> Cancel</SecondaryButton>

            <PrimaryButton
                :disabled="form.processing"
                @click="clearStudent">
                <LoaderCircle
                    v-if="form.processing"
                    class="h-4 w-4 animate-spin" />
                Clear Student
            </PrimaryButton>
        </div>
    </FormSection>
</template>
