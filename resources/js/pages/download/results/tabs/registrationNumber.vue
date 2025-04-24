<script lang="ts" setup>
import { PrimaryButton } from '@/components/buttons';
import { InputError, InputLabel, TextInput } from '@/components/inputs';
import { useForm } from '@inertiajs/vue3';
import { AlignButton, FormGroup, FormSection } from '@/components/forms';

const form = useForm({
    registration_number: '',
});

const submit = () => {
    form.post(route('download.results.registration-number.store'));
};
</script>

<template>
    <FormSection
        description="Input student's registration number to download results records"
        header="Download Result Information">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <FormGroup>
                <div class="grid flex-1 gap-2">
                    <InputLabel for="registration_number">Registration Number</InputLabel>

                    <TextInput
                        id="registration_number"
                        v-model="form.registration_number"
                        autocomplete="registration_number"
                        autofocus
                        placeholder="EBSU/2009/51486"
                        required
                        type="text" />

                    <InputError :message="form.errors.registration_number" />
                </div>

                <AlignButton>
                    <PrimaryButton :disabled="form.processing">Download</PrimaryButton>
                </AlignButton>
            </FormGroup>
        </form>
    </FormSection>
</template>
