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
    phone_number: props.student.others.phoneNumber,
    remark: '',
});

const title = `Update Student's Phone Number (${props.student.basic.registrationNumber})`;

const canNotUpdate = computed(() => props.student.others.phoneNumber === form.phone_number || form.processing);

const submit = () =>
    form.patch(route('student.phoneNumber.update', { student: props.student.basic.slug }), {
        onSuccess: () => emit('close'),
    });
</script>

<template>
    <BaseFormSection
        :header="title"
        description="Update student's phone number and submit">
        <form
            class="mt-6 space-y-6"
            @submit.prevent="submit">
            <div class="">
                <InputLabel
                    for="phone_number"
                    value="Phone Number" />

                <TextInput
                    id="phone_number"
                    v-model="form.phone_number"
                    autocomplete="off"
                    autofocus
                    required
                    type="text" />

                <InputError :message="form.errors.phone_number" />
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
