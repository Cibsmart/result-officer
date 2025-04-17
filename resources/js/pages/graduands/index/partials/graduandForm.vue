<script lang="ts" setup>
import { PrimaryButton } from '@/components/buttons';
import InputLabel from '@/components/inputs/inputLabel.vue';
import InputError from '@/components/inputs/inputError.vue';
import { useForm } from '@inertiajs/vue3';
import SelectInput from '@/components/inputs/selectInput.vue';
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
                <div class="flex-1">
                    <InputLabel
                        for="department"
                        value="Department" />

                    <SelectInput
                        v-if="!isLoading"
                        id="department"
                        v-model="form.department_object"
                        :items="departments" />

                    <SelectInput
                        v-else
                        id="department_loading"
                        :items="departments" />

                    <InputError
                        :message="form.errors.department"
                        class="mt-2" />
                </div>

                <AlignButton>
                    <PrimaryButton :disabled="form.processing"> View</PrimaryButton>
                </AlignButton>
            </div>
        </form>
    </FormSection>
</template>
