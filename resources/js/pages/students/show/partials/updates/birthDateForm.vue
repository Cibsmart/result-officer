<script lang="ts" setup>
import BaseFormSection from '@/components/forms/baseFormSection.vue';
import TextInput from '@/components/inputs/textInput.vue';
import InputError from '@/components/inputs/inputError.vue';
import InputLabel from '@/components/inputs/inputLabel.vue';
import PrimaryButton from '@/components/buttons/primaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import SecondaryButton from '@/components/buttons/secondaryButton.vue';
import { computed } from 'vue';
import CardFooter from '@/components/cards/cardFooter.vue';
import TextareaInput from '@/components/inputs/textareaInput.vue';

const props = defineProps<{
    student: App.Data.Students.StudentData;
}>();

const emit = defineEmits<(e: 'close') => void>();

const form = useForm({
    date_of_birth: props.student.basic.birthDate,
    remark: '',
});

const title = `Update Student's Date of Birth (${props.student.basic.registrationNumber})`;

const canNotUpdate = computed(() => props.student.basic.birthDate === form.date_of_birth || form.processing);

const submit = () =>
    form.patch(route('student.birthDate.update', { student: props.student.basic.slug }), {
        onSuccess: () => emit('close'),
    });
</script>

<template>
    <BaseFormSection
        :header="title"
        description="Update student's date of birth and submit">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <div class="">
                <InputLabel
                    for="date_of_birth"
                    value="Date of Birth" />

                <TextInput
                    id="date_of_birth"
                    v-model="form.date_of_birth"
                    autocomplete="off"
                    autofocus
                    placeholder="YYYY-MM-DD"
                    required
                    type="text" />

                <InputError :message="form.errors.date_of_birth" />
            </div>

            <div class="">
                <InputLabel
                    for="remark"
                    value="Remark (state action performed)" />

                <TextareaInput
                    id="remark"
                    v-model="form.remark"
                    required />

                <InputError :message="form.errors.remark" />
            </div>

            <CardFooter class="mt-6">
                <div class="mt-2 flex justify-end">
                    <SecondaryButton @click="emit('close')">Cancel</SecondaryButton>

                    <PrimaryButton
                        :disabled="canNotUpdate"
                        class="ms-3">
                        Update
                    </PrimaryButton>
                </div>
            </CardFooter>
        </form>
    </BaseFormSection>
</template>
