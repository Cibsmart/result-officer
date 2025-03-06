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
    departments: SelectItem[];
    sessions: SelectItem[];
    levels: SelectItem[];
}>();

const form = useForm({
    department: '',
    session: '',
    level: '',
});

const submit = () => {
    form.post(route('download.results.department-session-level.store'));
};
</script>

<template>
    <BaseFormSection
        description="Select Department, Session and Level to download result records"
        header="Download Result Information">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <div class="flex-1">
                <InputLabel
                    for="department"
                    value="Department" />

                <SelectInput
                    id="department"
                    v-model="form.department"
                    :items="departments" />

                <InputError :message="form.errors.department" />
            </div>

            <FormGroup>
                <div class="flex-1">
                    <InputLabel
                        for="session"
                        value="Session" />

                    <SelectInput
                        id="session"
                        v-model="form.session"
                        :items="sessions" />

                    <InputError :message="form.errors.session" />
                </div>

                <div class="flex-1">
                    <InputLabel
                        for="level"
                        value="Session" />

                    <SelectInput
                        id="level"
                        v-model="form.level"
                        :items="levels" />

                    <InputError :message="form.errors.level" />
                </div>

                <AlignButton>
                    <PrimaryButton :disabled="form.processing">Download</PrimaryButton>
                </AlignButton>
            </FormGroup>
        </form>
    </BaseFormSection>
</template>
