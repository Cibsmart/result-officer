<script lang="ts" setup>
import { InputError } from '@/components/inputs';
import { InputLabel } from '@/components/inputs';
import { TextInput } from '@/components/inputs';
import { Head, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { LoaderCircle } from 'lucide-vue-next';
import { PrimaryButton } from '@/components/buttons';

const props = defineProps<{
    email: string;
    token: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <AuthLayout
        description="Enter your email and password below to log in"
        title="Log in to your account">
        <Head title="Reset Password" />

        <form
            class="grid gap-4"
            @submit.prevent="submit">
            <div class="grid gap-2">
                <InputLabel for="email">Email </InputLabel>

                <TextInput
                    id="email"
                    v-model="form.email"
                    autocomplete="username"
                    autofocus
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
                <PrimaryButton :disabled="form.processing">
                    <LoaderCircle
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin" />
                    Reset Password
                </PrimaryButton>
            </div>
        </form>
    </AuthLayout>
</template>
