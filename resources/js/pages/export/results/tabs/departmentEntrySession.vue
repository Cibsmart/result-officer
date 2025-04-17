<script lang="ts" setup>
import InputLabel from '@/components/inputs/inputLabel.vue';
import { PrimaryButton } from '@/components/buttons';
import InputError from '@/components/inputs/inputError.vue';
import { useForm } from '@inertiajs/vue3';
import SelectInput from '@/components/inputs/selectInput.vue';
import { SelectItem } from '@/types';
import { AlignButton, FormGroup, FormSection } from '@/components/forms';

defineProps<{
    departments: SelectItem[];
    sessions: SelectItem[];
}>();

const form = useForm({
    department: '',
    session: '',
});

const submit = () => {
    form.post(route('export.results.department-session.store'), { onSuccess: () => download() });
};

const download = () => {
    window.location.href = route('export.results.department-session.download', {
        department: form.department,
        session: form.session,
    });
};
</script>

<template>
    <FormSection
        description="Select Department and Entry Session to download results records"
        header="Download Result Information">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <FormGroup>
                <div class="flex-1">
                    <InputLabel
                        for="department"
                        value="Department" />

                    <SelectInput
                        id="department"
                        v-model="form.department"
                        :items="departments"
                        class="mt-1 block w-full" />

                    <InputError
                        :message="form.errors.department"
                        class="mt-2" />
                </div>

                <div class="flex-1">
                    <InputLabel
                        for="session"
                        value="Entry Session" />

                    <SelectInput
                        id="session"
                        v-model="form.session"
                        :items="sessions"
                        class="mt-1 block w-full" />

                    <InputError
                        :message="form.errors.session"
                        class="mt-2" />
                </div>

                <AlignButton>
                    <PrimaryButton :disabled="form.processing">Export</PrimaryButton>
                </AlignButton>
            </FormGroup>
        </form>
    </FormSection>
</template>
