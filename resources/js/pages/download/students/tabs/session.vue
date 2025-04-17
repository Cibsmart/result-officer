<script lang="ts" setup>
import InputLabel from '@/components/inputs/inputLabel.vue';
import InputError from '@/components/inputs/inputError.vue';
import { useForm } from '@inertiajs/vue3';
import SelectInput from '@/components/inputs/selectInput.vue';
import { SelectItem } from '@/types';
import { FormSection } from '@/components/forms';
import { FormGroup } from '@/components/forms';
import { AlignButton } from '@/components/forms';
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
    </FormSection>
</template>
