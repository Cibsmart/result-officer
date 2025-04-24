<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import { BreadcrumbItem } from '@/types';
import VettingForm from '@/pages/graduands/index/partials/graduandForm.vue';
import StudentList from '@/pages/graduands/index/partials/graduandList.vue';
import { PaginatedGraduandListData } from '@/types/paginate';
import AppPage from '@/components/AppPage.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card } from '@/components/ui/card';

defineProps<{
    department: App.Data.Department.DepartmentInfoData;
    data: PaginatedGraduandListData | null;
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Graduands', href: route('graduand.index') }];
</script>

<template>
    <Head title="Possible Graduands Page" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AppPage
            description="View List of Possible Graduands per Department"
            title="Possible Graduands">
            <Card class="p-6">
                <VettingForm />
            </Card>

            <StudentList
                v-if="data !== null"
                :department="department"
                :paginated="data" />
        </AppPage>
    </AppLayout>
</template>
