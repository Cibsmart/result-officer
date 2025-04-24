<script lang="ts" setup>
import { InputError, InputLabel, SelectInput, TextareaInput, TextInput } from '@/components/inputs';
import { useForm } from '@inertiajs/vue3';
import { FormSection } from '@/components/forms';
import { useDepartments } from '@/composables/departments';
import { PrimaryButton } from '@/components/buttons';

const form = useForm({
    title: '',
    department: '',
    registration_numbers: '',
    department_object: { id: '' },
});

const submit = () => {
    form.transform((data) => ({ ...data, department: data.department_object.id })).post(route('vettingEvent.store'), {
        onSuccess: () => form.reset(),
    });
};

const { departments, isLoading } = useDepartments(true);
</script>

<template>
    <FormSection
        description="Enter Vetting Title, select Department and enter/paste list of Registration Numbers to be vetted"
        header="Vet Students' Results">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <div class="grid flex-1 gap-2">
                <InputLabel for="title">Title </InputLabel>

                <TextInput
                    id="title"
                    v-model="form.title"
                    placeholder="CSC 2009 - JULY 2013 or CSC 2019 (SUPPLEMENTARY) - NOV 2024"
                    required />

                <InputError :message="form.errors.title" />
            </div>

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

            <div class="grid flex-1 gap-2">
                <InputLabel for="registration_numbers">Registration Numbers </InputLabel>

                <TextareaInput
                    id="registration_numbers"
                    v-model="form.registration_numbers"
                    autocomplete="off"
                    placeholder="Enter or paste list of registration numbers"
                    required />

                <InputError :message="form.errors.registration_numbers" />
            </div>

            <PrimaryButton :disabled="form.processing"> Vet</PrimaryButton>
        </form>
    </FormSection>
</template>
