<script lang="ts" setup>
import TextInput from '@/components/inputs/textInput.vue';
import InputLabel from '@/components/inputs/inputLabel.vue';
import InputError from '@/components/inputs/inputError.vue';
import { useForm } from '@inertiajs/vue3';
import { AlignButton, FormGroup, FormSection } from '@/components/forms';
import { PrimaryButton } from '@/components/buttons';

const form = useForm({ registration_number: '' });

const submit = () => {
    form.post(route('finalResults.store'));
};
</script>

<template>
    <FormSection
        description="Input student's registration number to view final results"
        header="Final Result Information">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <FormGroup>
                <div class="flex-1">
                    <InputLabel
                        for="registration_number"
                        value="Registration Number" />

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
                    <PrimaryButton :disabled="form.processing">View</PrimaryButton>
                </AlignButton>
            </FormGroup>
        </form>
    </FormSection>
</template>
