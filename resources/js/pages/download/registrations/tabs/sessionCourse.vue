<script lang="ts" setup>
import InputLabel from '@/components/inputs/inputLabel.vue';
import InputError from '@/components/inputs/inputError.vue';
import { useForm } from '@inertiajs/vue3';
import SelectInput from '@/components/inputs/selectInput.vue';
import { SelectItem } from '@/types';
import SelectInputSearchable from '@/components/inputs/selectInputSearchable.vue';
import { FormSection } from '@/components/forms';
import { FormGroup } from '@/components/forms';
import { AlignButton } from '@/components/forms';
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
    form.post(route('download.registrations.session-course.store'));
};
</script>

<template>
    <FormSection
        description="Select Session and Course to download course registration records"
        header="Download Course Registration Information">
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

                    <InputError :message="form.errors.session" />
                </div>

                <div class="flex-1">
                    <InputLabel
                        for="course"
                        value="Course" />

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
