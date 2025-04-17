<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import BasePage from '@/components/AppPage.vue';
import BaseHeader from '@/layouts/main/partials/baseHeader.vue';
import ResultSummaryForm from '@/pages/summary/form/partials/form.vue';
import BaseSection from '@/layouts/main/partials/baseSection.vue';
import { BreadcrumbsItem } from '@/types';
import Breadcrumb from '@/components/breadcrumb.vue';
import AppPage from '@/components/AppPage.vue';
import { Card, CardContent } from '@/components/ui/card';

defineProps<{
    department: App.Data.Department.DepartmentListData;
    session: App.Data.Session.SessionListData;
    level: App.Data.Level.LevelListData;
}>();

const pages: BreadcrumbsItem[] = [
    { name: 'Result Summary', href: route('summary.form'), current: route().current('summary.form') },
];
</script>

<template>
    <Head title="Department Result Summary" />

    <Breadcrumb :pages="pages" />

    <BaseHeader> Department Result Summary</BaseHeader>

    <AppPage
        description="View Department's Result Summary per Session and Level"
        title="Result Summary Page">
        <Card class="p-6">
            <ResultSummaryForm
                :departments="department.data"
                :levels="level.levels"
                :sessions="session.sessions" />
        </Card>
    </AppPage>
</template>
