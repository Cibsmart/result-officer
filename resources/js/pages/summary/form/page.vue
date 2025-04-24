<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import ResultSummaryForm from '@/pages/summary/form/partials/form.vue';
import { BreadcrumbItem } from '@/types';
import AppPage from '@/components/AppPage.vue';
import { Card } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    department: App.Data.Department.DepartmentListData;
    session: App.Data.Session.SessionListData;
    level: App.Data.Level.LevelListData;
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Result Summary', href: route('summary.form') }];
</script>

<template>
    <Head title="Department Result Summary" />

    <AppLayout :breadcrumbs="breadcrumbs">
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
    </AppLayout>
</template>
