<script lang="ts" setup>
import UpdatePasswordForm from '@/pages/profile/partials/updatePasswordForm.vue';
import UpdateProfileInformationForm from '@/pages/profile/partials/updateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import BaseHeader from '@/layouts/main/partials/baseHeader.vue';
import BasePage from '@/layouts/main/partials/basePage.vue';
import BaseSection from '@/layouts/main/partials/baseSection.vue';
import Breadcrumb from '@/components/breadcrumb.vue';
import { BreadcrumbItem } from '@/types';

defineProps<{
    status?: string;
}>();

const pages: BreadcrumbItem[] = [
    {
        name: 'Profile',
        href: route('profile.edit'),
        current: route().current('profile.edit'),
    },
];
</script>

<template>
    <Head title="Profile" />

    <Breadcrumb :pages="pages" />

    <BaseHeader>Profile</BaseHeader>

    <BasePage>
        <BaseSection>
            <UpdateProfileInformationForm
                :status="status"
                class="max-w-xl" />
        </BaseSection>

        <BaseSection>
            <UpdatePasswordForm class="max-w-xl" />
        </BaseSection>

        <!--    <BaseSection>-->
        <!--      <DeleteUserForm class="max-w-xl" />-->
        <!--    </BaseSection>-->
    </BasePage>
</template>
