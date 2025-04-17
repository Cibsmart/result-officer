<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import { BreadcrumbItem } from '@/types';
import ClearedForm from '@/pages/reports/cleared/index/partials/clearedForm.vue';
import { computed } from 'vue';
import ClearedList from '@/pages/reports/cleared/index/partials/clearedList.vue';
import AppPage from '@/components/AppPage.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card } from '@/components/ui/card';

const props = defineProps<{
    departments: App.Data.Department.DepartmentListData;
    students: App.Data.Cleared.ClearedStudentListData;
}>();

const hasClearedStudents = computed(() => props.students !== null);

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Cleared', href: route('department.cleared.index') }];
</script>

<template>
    <Head title="Cleared Students" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AppPage
            description="List of Cleared Students per Department"
            title="Cleared Students">
            <Card class="p-6">
                <ClearedForm :departments="departments.data" />
            </Card>

            <Card v-if="hasClearedStudents">
                <ClearedList :students="students" />
            </Card>
        </AppPage>
    </AppLayout>
</template>
