<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import CompositeForm from '@/pages/reports/composite/form/partials/form.vue';
import { BreadcrumbItem } from '@/types';
import AppPage from '@/components/AppPage.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card } from '@/components/ui/card';

defineProps<{
    semester: App.Data.Semester.SemesterListData;
    session: App.Data.Session.SessionListData;
    program: App.Data.Program.ProgramListData;
    level: App.Data.Level.LevelListData;
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Composite Sheet', href: route('composite.form') }];
</script>

<template>
    <Head title="View Composite Sheet" />

    <AppLayout :breadcrumbs>
        <AppPage
            description="View Department Composite Sheet by Session, Semester and Level"
            title="Composite Sheet">
            <Card class="p-6">
                <CompositeForm
                    :levels="level.levels"
                    :programs="program.programs"
                    :semesters="semester.semesters"
                    :sessions="session.sessions" />
            </Card>
        </AppPage>
    </AppLayout>
</template>
