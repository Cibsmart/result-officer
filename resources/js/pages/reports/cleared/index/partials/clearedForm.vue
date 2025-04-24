<script lang="ts" setup>
import { PrimaryButton } from '@/components/buttons';
import { InputError, InputLabel, SelectInput } from '@/components/inputs';
import { useForm } from '@inertiajs/vue3';
import { SelectItem } from '@/types';
import { AlignButton, FormGroup, FormSection } from '@/components/forms';
import { useMonths } from '@/composables/months';
import { useYears } from '@/composables/year';

defineProps<{ departments: SelectItem[] }>();

const form = useForm({
    department: '',
    year: '',
    month: '',
    department_object: { id: '' },
    month_object: { id: '' },
    year_object: { id: '' },
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        department: data.department_object.id,
        month: data.month_object.id,
        year: data.year_object.id,
    })).post(route('department.cleared.store'));
};

const { years } = useYears();

const { months } = useMonths();
</script>

<template>
    <FormSection
        description="Select Department, Year and Month to View List of Cleared Students"
        header="View Cleared Student">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <div class="flex-1">
                <div class="grid gap-2">
                    <InputLabel for="department">Department</InputLabel>

                    <SelectInput
                        id="department"
                        v-model="form.department_object"
                        :items="departments" />

                    <InputError :message="form.errors.department" />
                </div>
            </div>

            <FormGroup>
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

                <AlignButton>
                    <PrimaryButton :disabled="form.processing"> View</PrimaryButton>
                </AlignButton>
            </FormGroup>
        </form>
    </FormSection>
</template>
