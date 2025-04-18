<script lang="ts" setup>
import { InputError, InputLabel, SelectInput } from '@/components/inputs';
import { useForm } from '@inertiajs/vue3';
import SelectInputSearchable from '@/components/inputs/selectInputSearchable.vue';
import { SelectItem } from '@/types';
import { AlignButton, FormGroup, FormSection } from '@/components/forms';
import { PrimaryButton } from '@/components/buttons';

defineProps<{
    sessions: SelectItem[];
    courses: SelectItem[];
}>();

const form = useForm({
    session: '',
    course: '',
});

const submit = () => {
    form.post(route('download.results.session-course.store'));
};
</script>

<template>
    <FormSection
        description="Select session and course to download result records"
        header="Download Result Information">
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

                <div class="grid flex-1 gap-2">
                    <InputLabel for="course">Course</InputLabel>

                    <SelectInputSearchable
                        id="course"
                        v-model="form.course"
                        :items="courses"
                        name="courses" />

                    <InputError :message="form.errors.course" />
                </div>

                <AlignButton>
                    <PrimaryButton :disabled="form.processing">Download</PrimaryButton>
                </AlignButton>
            </FormGroup>
        </form>
    </FormSection>
</template>
