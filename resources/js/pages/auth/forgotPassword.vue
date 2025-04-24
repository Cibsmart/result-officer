<script lang="ts" setup>
import { InputError } from '@/components/inputs';
import { InputLabel } from '@/components/inputs';
import { PrimaryButton } from '@/components/buttons';
import { TextInput } from '@/components/inputs';
import { Head, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthLayout
        description="Enter your email to receive a password reset link"
        title="Forgot password">
        <Head title="Forgot Password" />

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Forgot your password? No problem. Just let us know your email address and we will email you a password reset
            link that will allow you to choose a new one.
        </div>

        <div
            v-if="status"
            class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div class="grid gap-2">
                <InputLabel for="email">Email</InputLabel>

                <TextInput
                    id="email"
                    v-model="form.email"
                    autocomplete="username"
                    autofocus
                    required
                    type="email" />

                <InputError :message="form.errors.email" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton :disabled="form.processing">
                    <LoaderCircle
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin" />
                    Email Password Reset Link
                </PrimaryButton>
            </div>
        </form>
    </AuthLayout>
</template>
