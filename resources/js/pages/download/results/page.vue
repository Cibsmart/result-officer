<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import { BreadcrumbItem, TabItem } from '@/types';
import RegistrationNumber from '@/pages/download/results/tabs/registrationNumber.vue';
import DepartmentSessionLevel from '@/pages/download/results/tabs/departmentSessionLevel.vue';
import DepartmentSessionSemester from '@/pages/download/results/tabs/departmentSessionSemester.vue';
import SessionCourse from '@/pages/download/results/tabs/sessionCourse.vue';
import BaseTabs from '@/components/tabs/baseTabs.vue';
import BaseTabPanel from '@/components/tabs/baseTabPanel.vue';
import ImportEvents from '@/pages/download/components/importEvents.vue';
import DepartmentEntrySession from '@/pages/download/results/tabs/departmentEntrySession.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import AppPage from '@/components/AppPage.vue';
import { Card } from '@/components/ui/card';

defineProps<{
    departments: App.Data.Department.DepartmentListData;
    sessions: App.Data.Session.SessionListData;
    semesters: App.Data.Semester.SemesterListData;
    courses: App.Data.Course.CourseListData;
    levels: App.Data.Level.LevelListData;
    events: Array<App.Data.Imports.ImportEventData>;
    pending: App.Data.Imports.PendingImportEventData;
    selectedIndex: number;
}>();

const tabs: TabItem[] = [
    { name: 'By Reg No.' },
    { name: 'By Dept & Session' },
    { name: 'By Dept Session & Level' },
    { name: 'By Dept Session & Semester' },
    { name: 'By Session & Course' },
];

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Result Download', href: route('download.results.page') }];
</script>

<template>
    <Head title="Download Result Records" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AppPage
            description="Select tab and complete form to Download Results from the Portal"
            title="Download Results">
            <Card>
                <BaseTabs
                    :selectedIndex="selectedIndex"
                    :tabs="tabs">
                    <BaseTabPanel>
                        <RegistrationNumber />
                    </BaseTabPanel>

                    <BaseTabPanel>
                        <DepartmentEntrySession
                            :departments="departments.data"
                            :sessions="sessions.sessions" />
                    </BaseTabPanel>

                    <BaseTabPanel>
                        <DepartmentSessionLevel
                            :departments="departments.data"
                            :levels="levels.levels"
                            :sessions="sessions.sessions" />
                    </BaseTabPanel>

                    <BaseTabPanel>
                        <DepartmentSessionSemester
                            :departments="departments.data"
                            :semesters="semesters.semesters"
                            :sessions="sessions.sessions" />
                    </BaseTabPanel>

                    <BaseTabPanel>
                        <SessionCourse
                            :courses="courses.courses"
                            :sessions="sessions.sessions" />
                    </BaseTabPanel>
                </BaseTabs>
            </Card>

            <ImportEvents
                :events="events"
                :pending="pending" />
        </AppPage>
    </AppLayout>
</template>
