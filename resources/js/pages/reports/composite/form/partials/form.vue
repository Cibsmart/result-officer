<script lang="ts" setup>
import { PrimaryButton } from '@/components/buttons';
import { InputError, InputLabel, SelectInput } from '@/components/inputs';
import { useForm } from '@inertiajs/vue3';
import { SelectItem } from '@/types';
import { FormGroup, FormSection } from '@/components/forms';

defineProps<{
    programs: SelectItem[];
    semesters: SelectItem[];
    sessions: SelectItem[];
    levels: SelectItem[];
}>();

const form = useForm({
    program: '',
    session: '',
    semester: '',
    level: '',
});

const submit = () => {
    form.post(route('composite.view'));
};
</script>

<template>
    <FormSection
        description="Select Department, Session Semester and Level to view the Department Composite Sheet"
        header="Department Composite Sheet">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <FormGroup>
                <div class="grid flex-1 gap-2">
                    <InputLabel for="program">Program</InputLabel>

                    <SelectInput
                        id="program"
                        v-model="form.program"
                        :items="programs" />

                    <InputError :message="form.errors.program" />
                </div>

                <div class="grid flex-1 gap-2">
                    <InputLabel for="session">Session</InputLabel>

                    <SelectInput
                        id="session"
                        v-model="form.session"
                        :items="sessions" />

                    <InputError :message="form.errors.session" />
                </div>
            </FormGroup>

            <FormGroup>
                <div class="grid flex-1 gap-2">
                    <InputLabel for="semester">Semester</InputLabel>

                    <SelectInput
                        id="semester"
                        v-model="form.semester"
                        :items="semesters" />

                    <InputError :message="form.errors.semester" />
                </div>

                <div class="grid flex-1 gap-2">
                    <InputLabel for="level">Level</InputLabel>

                    <SelectInput
                        id="level"
                        v-model="form.level"
                        :items="levels" />

                    <InputError :message="form.errors.level" />
                </div>
            </FormGroup>

            <div>
                <PrimaryButton :disabled="form.processing">View</PrimaryButton>
            </div>
        </form>
    </FormSection>
</template>
