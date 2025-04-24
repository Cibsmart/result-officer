<script lang="ts" setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/AuthLayout.vue';
import TextLink from '@/components/TextLink.vue';
import { PrimaryButton } from '@/components/buttons';
import { Checkbox } from '@/components/ui/checkbox';
import { LoaderCircle } from 'lucide-vue-next';
import { InputLabel, TextInput, InputError } from '@/components/inputs';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <AuthLayout
        description="Enter your email and password below to log in"
        title="Log in to your account">
        <Head title="Log in" />

        <div
            v-if="status"
            class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form
            class="flex flex-col gap-6"
            @submit.prevent="submit">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <InputLabel for="email">Email address</InputLabel>

                    <TextInput
                        id="email"
                        v-model="form.email"
                        :tabindex="1"
                        autocomplete="email"
                        autofocus
                        placeholder="email@example.com"
                        required
                        type="email" />

                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <InputLabel for="password">Password</InputLabel>

                        <TextLink
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            :tabindex="5"
                            class="text-sm">
                            Forgot password?
                        </TextLink>
                    </div>

                    <TextInput
                        id="password"
                        v-model="form.password"
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="Password"
                        required
                        type="password" />

                    <InputError :message="form.errors.password" />
                </div>

                <div
                    :tabindex="3"
                    class="flex items-center justify-between">
                    <InputLabel
                        class="flex items-center space-x-3"
                        for="remember">
                        <Checkbox
                            id="remember"
                            v-model="form.remember"
                            :tabindex="4" />

                        <span>Remember me</span>
                    </InputLabel>
                </div>

                <PrimaryButton
                    :disabled="form.processing"
                    :tabindex="4"
                    class="w-full">
                    <LoaderCircle
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin" />
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </AuthLayout>
</template>
