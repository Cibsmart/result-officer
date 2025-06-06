<script lang="ts" setup>
import { FormSection } from '@/components/forms';
import { InputError, InputLabel, SelectInput, TextareaInput } from '@/components/inputs';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useGenders } from '@/composables/genders';
import { PrimaryButton, SecondaryButton } from '@/components/buttons';

const props = defineProps<{
    student: App.Data.Students.StudentData;
}>();

const emit = defineEmits<(e: 'close') => void>();

const { genders } = useGenders();

const form = useForm({
    gender: props.student.basic.gender,
    gender_object: genders['0'],
    remark: '',
});

const title = `Update Student's Gender (${props.student.basic.registrationNumber})`;

const canNotUpdate = computed(() => props.student.basic.gender === form.gender_object.id || form.processing);

const submit = () =>
    form
        .transform((data) => ({ ...data, gender: data.gender_object.id }))
        .patch(route('student.gender.update', { student: props.student.basic.slug }), {
            onSuccess: () => emit('close'),
        });

const selected = computed(() => {
    const gender = props.student.basic.gender;
    return gender === 'U' ? '0' : props.student.basic.gender;
});
</script>

<template>
    <FormSection
        :header="title"
        description="Update student's gender and submit">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <div class="grid gap-2">
                <InputLabel for="gender">Gender</InputLabel>

                <SelectInput
                    id="gender"
                    v-model="form.gender_object"
                    :items="genders"
                    :selected="selected" />

                <InputError :message="form.errors.gender" />
            </div>

            <div class="grid gap-2">
                <InputLabel for="remark">Remark (state action performed)</InputLabel>

                <TextareaInput
                    id="remark"
                    v-model="form.remark"
                    required />

                <InputError :message="form.errors.remark" />
            </div>

            <div class="mt-2 flex justify-end gap-2">
                <SecondaryButton @click="emit('close')"> Cancel</SecondaryButton>

                <PrimaryButton :disabled="canNotUpdate"> Update</PrimaryButton>
            </div>
        </form>
    </FormSection>
</template>
