<script lang="ts" setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import { InputLabel, TextInput, InputError } from '@/components/inputs';
import { PrimaryButton } from '@/components/buttons';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Profile settings', href: route('profile.edit') }];

const page = usePage();
const user = page.props.user;

const form = useForm({
    name: user.name,
    email: user.email,
});

const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    description="Update your name and email address"
                    title="Profile information" />

                <form
                    class="space-y-6"
                    @submit.prevent="submit">
                    <div class="grid gap-2">
                        <InputLabel for="name">Name</InputLabel>

                        <TextInput
                            id="name"
                            v-model="form.name"
                            autocomplete="name"
                            disabled
                            placeholder="Full name"
                            required />

                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <InputLabel for="email">Email address</InputLabel>

                        <TextInput
                            id="email"
                            v-model="form.email"
                            autocomplete="username"
                            placeholder="Email address"
                            required
                            type="email" />

                        <InputError :message="form.errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.emailVerified">
                        <p class="text-muted-foreground -mt-4 text-sm">
                            Your email address is unverified.
                            <Link
                                :href="route('verification.send')"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                method="post">
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div
                            v-if="status === 'verification-link-sent'"
                            class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0">
                            <p
                                v-show="form.recentlySuccessful"
                                class="text-sm text-neutral-600">
                                Saved.
                            </p>
                        </Transition>
                    </div>
                </form>
            </div>

            <!--            <DeleteUser />-->
        </SettingsLayout>
    </AppLayout>
</template>
