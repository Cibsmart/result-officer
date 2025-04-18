<script lang="ts" setup>
import { InputError, InputLabel, SelectInput } from '@/components/inputs';
import { PrimaryButton } from '@/components/buttons';
import { useForm } from '@inertiajs/vue3';
import { SelectItem } from '@/types';
import { AlignButton, FormGroup, FormSection } from '@/components/forms';

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
    <FormSection
        description="Select Department, Session and Level to download result records"
        header="Download Result Information">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <div class="grid flex-1 gap-2">
                <InputLabel for="department">Department </InputLabel>

                <SelectInput
                    id="department"
                    v-model="form.department"
                    :items="departments" />

                <InputError :message="form.errors.department" />
            </div>

            <FormGroup>
                <div class="grid flex-1 gap-2">
                    <InputLabel for="session">Session</InputLabel>

                    <SelectInput
                        id="session"
                        v-model="form.session"
                        :items="sessions" />

                    <InputError :message="form.errors.session" />
                </div>

                <div class="grid flex-1 gap-2">
                    <InputLabel for="level">Level</InputLabel>

                    <SelectInput
                        id="level"
                        v-model="form.level"
                        :items="levels" />

                    <InputError :message="form.errors.level" />
                </div>

                <AlignButton>
                    <PrimaryButton :disabled="form.processing"> Download</PrimaryButton>
                </AlignButton>
            </FormGroup>
        </form>
    </FormSection>
</template>
