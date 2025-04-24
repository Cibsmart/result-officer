<script lang="ts" setup>
import { InputError, InputLabel, TextInput } from '@/components/inputs';
import { PrimaryButton } from '@/components/buttons';
import { Head, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AuthLayout
        description="Please enter your new password below"
        title="Reset password">
        <Head title="Confirm Password" />

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            This is a secure area of the application. Please confirm your password before continuing.
        </div>

        <form @submit.prevent="submit">
            <div class="grid gap-2">
                <InputLabel for="password">Password</InputLabel>

                <TextInput
                    id="password"
                    v-model="form.password"
                    autocomplete="current-password"
                    autofocus
                    required
                    type="password" />

                <InputError :message="form.errors.password" />
            </div>

            <div class="mt-4 flex justify-end">
                <PrimaryButton :disabled="form.processing">
                    <LoaderCircle
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin" />
                    Confirm
                </PrimaryButton>
            </div>
        </form>
    </AuthLayout>
</template>
