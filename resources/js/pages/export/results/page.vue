<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import { BreadcrumbItem, TabItem } from '@/types';
import RegistrationNumber from '@/pages/export/results/tabs/registrationNumber.vue';
import BaseTabPanel from '@/components/tabs/baseTabPanel.vue';
import DepartmentEntrySession from '@/pages/export/results/tabs/departmentEntrySession.vue';
import BasePage from '@/layouts/main/partials/basePage.vue';
import BaseTabs from '@/components/tabs/baseTabs.vue';
import BaseHeader from '@/layouts/main/partials/baseHeader.vue';
import Breadcrumb from '@/components/breadcrumb.vue';
import BaseSection from '@/layouts/main/partials/baseSection.vue';
import RegistrationNumbers from '@/pages/export/results/tabs/registrationNumbers.vue';

defineProps<{
    departments: App.Data.Department.DepartmentListData;
    sessions: App.Data.Session.SessionListData;
    selectedIndex: number;
}>();

const pages: BreadcrumbItem[] = [
    {
        name: 'Results Export',
        href: route('export.results.page', { selectedIndex: 1 }),
        current: route().current('export.results.page'),
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

    <Breadcrumb :pages="pages" />

    <BaseHeader>Export Result Records</BaseHeader>

    <BasePage>
        <BaseSection>
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
        </BaseSection>
    </BasePage>
</template>
