<script lang="ts" setup>
import UpdatePasswordForm from '@/pages/profile/partials/updatePasswordForm.vue';
import UpdateProfileInformationForm from '@/pages/profile/partials/updateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { BreadcrumbItem } from '@/types';
import AppPage from '@/components/AppPage.vue';
import { Card } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    status?: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Profile', href: route('profile.edit') }];
</script>

<template>
    <Head title="Profile" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AppPage
            description="Update profile information"
            title="Profile">
            <Card>
                <UpdateProfileInformationForm
                    :status="status"
                    class="max-w-xl" />
            </Card>

            <Card>
                <UpdatePasswordForm class="max-w-xl" />
            </Card>

            <!--    <BaseSection>-->
            <!--      <DeleteUserForm class="max-w-xl" />-->
            <!--    </BaseSection>-->
        </AppPage>
    </AppLayout>
</template>
