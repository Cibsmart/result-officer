<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import RegistrationNumber from '@/pages/download/students/tabs/registrationNumber.vue';
import { BreadcrumbItem, TabItem } from '@/types';
import BaseTabs from '@/components/tabs/baseTabs.vue';
import DepartmentSession from '@/pages/download/students/tabs/departmentSession.vue';
import Session from '@/pages/download/students/tabs/session.vue';
import BaseTabPanel from '@/components/tabs/baseTabPanel.vue';
import ImportEvents from '@/pages/download/components/importEvents.vue';
import AppPage from '@/components/AppPage.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card } from '@/components/ui/card';

defineProps<{
    department: App.Data.Department.DepartmentListData;
    session: App.Data.Session.SessionListData;
    events: Array<App.Data.Imports.ImportEventData>;
    pending: App.Data.Imports.PendingImportEventData;
    selectedIndex: number;
}>();

const tabs: TabItem[] = [
    { name: 'By Registration Number' },
    { name: 'By Department and Session' },
    { name: 'By Session' },
];

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Student Download', href: route('download.students.page') }];
</script>

<template>
    <Head title="Download Student Record" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AppPage
            description="Select tab and complete form to Download Students from the Portal"
            title="Download Students">
            <Card>
                <BaseTabs
                    :selectedIndex="selectedIndex"
                    :tabs="tabs">
                    <BaseTabPanel>
                        <RegistrationNumber />
                    </BaseTabPanel>

                    <BaseTabPanel>
                        <DepartmentSession
                            :departments="department.data"
                            :sessions="session.sessions" />
                    </BaseTabPanel>

                    <BaseTabPanel>
                        <Session :sessions="session.sessions" />
                    </BaseTabPanel>
                </BaseTabs>
            </Card>

            <ImportEvents
                :events="events"
                :pending="pending" />
        </AppPage>
    </AppLayout>
</template>
