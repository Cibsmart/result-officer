<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import { BreadcrumbsItem } from '@/types';
import BaseHeader from '@/layouts/main/partials/baseHeader.vue';
import Breadcrumb from '@/components/breadcrumb.vue';
import BasePage from '@/layouts/main/partials/basePage.vue';
import BaseSection from '@/layouts/main/partials/baseSection.vue';
import VettingForm from '@/pages/graduands/index/partials/graduandForm.vue';
import StudentList from '@/pages/graduands/index/partials/graduandList.vue';
import { PaginatedGraduandListData } from '@/types/paginate';

defineProps<{
    department: App.Data.Department.DepartmentInfoData;
    data: PaginatedGraduandListData | null;
}>();

const pages: BreadcrumbsItem[] = [
    { name: 'Graduands', href: route('graduand.index'), current: route().current('graduand.index') },
];
</script>

<template>
    <Head title="Possible Graduands Page" />

    <Breadcrumb :pages="pages" />

    <BaseHeader> View List of Possible Graduands</BaseHeader>

    <BasePage>
        <BaseSection>
            <VettingForm />
        </BaseSection>

        <StudentList
            v-if="data !== null"
            :department="department"
            :paginated="data" />
    </BasePage>
</template>
