<script lang="ts" setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { LoaderCircle } from 'lucide-vue-next';
import { PrimaryButton } from '@/components/buttons';
import { SecondaryLink } from '@/components/links';

const props = defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <AuthLayout
        description="Please verify your email address by clicking on the link we just emailed to you."
        title="Verify email">
        <Head title="Email Verification" />

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link
            we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </div>

        <div
            v-if="verificationLinkSent"
            class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
            A new verification link has been sent to the email address you provided during registration.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton :disabled="form.processing">
                    <LoaderCircle
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin" />
                    Resend Verification Email
                </PrimaryButton>

                <SecondaryLink
                    :href="route('logout')"
                    as="button"
                    method="post">
                    Log Out
                </SecondaryLink>
            </div>
        </form>
    </AuthLayout>
</template>
