<script lang="ts" setup>
import InputLabel from '@/components/inputs/inputLabel.vue';
import PrimaryButton from '@/components/buttons/primaryButton.vue';
import InputError from '@/components/inputs/inputError.vue';
import { useForm } from '@inertiajs/vue3';
import SelectInput from '@/components/inputs/selectInput.vue';
import { SelectItem } from '@/types';
import BaseFormSection from '@/components/forms/baseFormSection.vue';
import FormGroup from '@/components/forms/formGroup.vue';
import AlignButton from '@/components/forms/alignButton.vue';

defineProps<{
    sessions: SelectItem[];
}>();

const form = useForm({
    session: '',
});

const submit = () => {
    form.post(route('download.students.session.store'));
};
</script>

<template>
    <BaseFormSection
        description="Select Session to download their records"
        header="Download Students Information">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <FormGroup>
                <div class="flex-1">
                    <InputLabel
                        for="session"
                        value="Session" />

                    <SelectInput
                        id="session"
                        v-model="form.session"
                        :items="sessions" />

                    <InputError
                        :message="form.errors.session"
                        class="mt-2" />
                </div>

                <AlignButton>
                    <PrimaryButton :disabled="form.processing">Download</PrimaryButton>
                </AlignButton>
            </FormGroup>
        </form>
    </BaseFormSection>
</template>
