<script lang="ts" setup>
import { InputError, InputLabel, TextareaInput } from '@/components/inputs';
import { useForm } from '@inertiajs/vue3';
import { AlignButton, FormGroup, FormSection } from '@/components/forms';
import { PrimaryButton } from '@/components/buttons';

const form = useForm({ registration_numbers: '' });

const submit = () => {
    form.post(route('export.results.registration-numbers.store'), {
        onSuccess: () => {
            download();
            form.reset();
        },
    });
};

const download = () => {
    window.location.href = route('export.results.registration-numbers.download', {
        registration_numbers: form.registration_numbers,
    });
};
</script>

<template>
    <FormSection
        description="Input students' Registration Numbers to export results records"
        header="Export Result Information">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <FormGroup>
                <div class="grid flex-1 gap-2">
                    <InputLabel for="registration_numbers">Registration Numbers</InputLabel>

                    <TextareaInput
                        id="registration_numbers"
                        v-model="form.registration_numbers"
                        autocomplete="registration_numbers"
                        autofocus
                        placeholder="Enter comma separated List of Registration Numbers"
                        required
                        type="text" />

                    <InputError :message="form.errors.registration_numbers" />
                </div>

                <AlignButton>
                    <PrimaryButton :disabled="form.processing">Export</PrimaryButton>
                </AlignButton>
            </FormGroup>
        </form>
    </FormSection>
</template>
