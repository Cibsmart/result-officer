<script lang="ts" setup>
import { InputError, InputLabel, TextInput } from '@/components/inputs';
import { PrimaryButton } from '@/components/buttons';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <AuthLayout
        description="Enter your details below to create your account"
        title="Create an account">
        <Head title="Register" />

        <form
            class="grid gap-4"
            @submit.prevent="submit">
            <div class="grid gap-2">
                <InputLabel for="name">Name </InputLabel>

                <TextInput
                    id="name"
                    v-model="form.name"
                    autocomplete="name"
                    autofocus
                    required
                    type="text" />

                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-2">
                <InputLabel for="email">Email </InputLabel>

                <TextInput
                    id="email"
                    v-model="form.email"
                    autocomplete="username"
                    required
                    type="email" />

                <InputError :message="form.errors.email" />
            </div>

            <div class="grid gap-2">
                <InputLabel for="password">Password </InputLabel>

                <TextInput
                    id="password"
                    v-model="form.password"
                    autocomplete="new-password"
                    required
                    type="password" />

                <InputError :message="form.errors.password" />
            </div>

            <div class="grid gap-2">
                <InputLabel for="password_confirmation">Confirm Password </InputLabel>

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    autocomplete="new-password"
                    required
                    type="password" />

                <InputError :message="form.errors.password_confirmation" />
            </div>

            <div class="flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800">
                    Already registered?
                </Link>

                <PrimaryButton :disabled="form.processing">
                    <LoaderCircle
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin" />
                    Register
                </PrimaryButton>
            </div>
        </form>
    </AuthLayout>
</template>
