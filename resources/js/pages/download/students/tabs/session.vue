<script lang="ts" setup>
import { InputError, InputLabel, SelectInput } from '@/components/inputs';
import { useForm } from '@inertiajs/vue3';
import { SelectItem } from '@/types';
import { AlignButton, FormGroup, FormSection } from '@/components/forms';
import { PrimaryButton } from '@/components/buttons';

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
    <FormSection
        description="Select Session to download their records"
        header="Download Students Information">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <FormGroup>
                <div class="grid flex-1 gap-2">
                    <InputLabel for="session">Session</InputLabel>

                    <SelectInput
                        id="session"
                        v-model="form.session"
                        :items="sessions" />

                    <InputError :message="form.errors.session" />
                </div>

                <AlignButton>
                    <PrimaryButton :disabled="form.processing">Download</PrimaryButton>
                </AlignButton>
            </FormGroup>
        </form>
    </FormSection>
</template>
