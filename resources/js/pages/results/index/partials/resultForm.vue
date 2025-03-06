<script lang="ts" setup>
import PrimaryButton from '@/components/buttons/primaryButton.vue';
import TextInput from '@/components/inputs/textInput.vue';
import InputLabel from '@/components/inputs/inputLabel.vue';
import InputError from '@/components/inputs/inputError.vue';
import { useForm } from '@inertiajs/vue3';
import BaseFormSection from '@/components/forms/baseFormSection.vue';
import FormGroup from '@/components/forms/formGroup.vue';
import AlignButton from '@/components/forms/alignButton.vue';

const form = useForm({
    registration_number: '',
});

const submit = () => {
    form.post(route('results.store'));
};
</script>

<template>
    <BaseFormSection
        description="Input student's registration number to view results"
        header="Result Information">
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
    </BaseFormSection>
</template>
