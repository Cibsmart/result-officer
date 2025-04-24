<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import { BreadcrumbItem, TabItem } from '@/types';
import RegistrationNumber from '@/pages/export/results/tabs/registrationNumber.vue';
import BaseTabPanel from '@/components/tabs/baseTabPanel.vue';
import DepartmentEntrySession from '@/pages/export/results/tabs/departmentEntrySession.vue';
import BaseTabs from '@/components/tabs/baseTabs.vue';
import RegistrationNumbers from '@/pages/export/results/tabs/registrationNumbers.vue';
import AppPage from '@/components/AppPage.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card } from '@/components/ui/card';

defineProps<{
    departments: App.Data.Department.DepartmentListData;
    sessions: App.Data.Session.SessionListData;
    selectedIndex: number;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Results Export',
        href: route('export.results.page', { selectedIndex: 1 }),
    },
];

const tabs: TabItem[] = [
    { name: 'By Registration Number' },
    { name: 'By Registration Number List' },
    { name: 'By Dept and Entry Session' },
];
</script>

<template>
    <Head title="Export Result Records" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AppPage
            description="Export Result Records from the Database to Excel"
            title="Export Result Record">
            <Card>
                <BaseTabs
                    :selectedIndex="selectedIndex"
                    :tabs="tabs">
                    <BaseTabPanel>
                        <RegistrationNumber />
                    </BaseTabPanel>

                    <BaseTabPanel>
                        <RegistrationNumbers />
                    </BaseTabPanel>

                    <BaseTabPanel>
                        <DepartmentEntrySession
                            :departments="departments.data"
                            :sessions="sessions.sessions" />
                    </BaseTabPanel>
                </BaseTabs>
            </Card>
        </AppPage>
    </AppLayout>
</template>
