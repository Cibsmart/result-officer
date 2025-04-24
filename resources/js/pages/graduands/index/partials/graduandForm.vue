<script lang="ts" setup>
import { PrimaryButton } from '@/components/buttons';
import { InputError, InputLabel, SelectInput } from '@/components/inputs';
import { useForm } from '@inertiajs/vue3';
import { AlignButton, FormSection } from '@/components/forms';
import { useDepartments } from '@/composables/departments';

const form = useForm({
    department: '',
    department_object: { id: '' },
});

const submit = () => {
    form.transform((data) => ({ ...data, department: data.department_object.id })).post(route('graduand.store'));
};

const { departments, isLoading } = useDepartments();
</script>

<template>
    <FormSection
        description="Select Department and input Registration Number"
        header="Vetting List">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <div class="flex w-full items-start space-x-4">
                <div class="grid flex-1 gap-2">
                    <InputLabel for="department">Department </InputLabel>

                    <SelectInput
                        v-if="!isLoading"
                        id="department"
                        v-model="form.department_object"
                        :items="departments" />

                    <SelectInput
                        v-else
                        id="department_loading"
                        :items="departments" />

                    <InputError :message="form.errors.department" />
                </div>

                <AlignButton>
                    <PrimaryButton :disabled="form.processing"> View</PrimaryButton>
                </AlignButton>
            </div>
        </form>
    </FormSection>
</template>
