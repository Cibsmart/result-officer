<script lang="ts" setup>
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import Students from '@/pages/summary/view/partials/students.vue';
import EmptyState from '@/components/emptyState.vue';
import IconLink from '@/components/links/iconLink.vue';
import AppPage from '@/components/AppPage.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Printer } from 'lucide-vue-next';

const props = defineProps<{
    department: App.Data.Summary.DepartmentResultSummaryData;
}>();

const breadcumbs: BreadcrumbItem[] = [
    { title: 'Summary Form', href: route('summary.form') },
    { title: 'Summary View', href: route('summary.view') },
];

const hasSummary = computed(() => props.department.students.length > 0);
const description = computed(
    () =>
        `List of ${props.department.level.name} level ${props.department.department.name} students in ${props.department.session.name} with their current CGPA sorted from highest to lowest`,
);
</script>

<template>
    <Head title="View Department Result Summary" />

    <AppLayout :breadcrumbs="breadcumbs">
        <AppPage
            :description="description"
            title="Department Result Summary">
            <template #actions>
                <Button asChild>
                    <a
                        :href="
                            route('summary.print', {
                                department: department.department.slug,
                                session: department.session.slug,
                                level: department.level.slug,
                            })
                        "
                        target="_blank">
                        <Printer />
                        Print
                    </a>
                </Button>
            </template>

            <Card>
                <div>
                    <div
                        class="divide-y divide-solid divide-gray-300 ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:divide-gray-600 dark:ring-gray-600">
                        <div class="p-2">
                            DEPARTMENT:
                            <span class="font-bold text-black dark:text-white">{{ department.department.name }}</span>
                        </div>

                        <div class="p-2">
                            SESSION:
                            <span class="font-bold text-black dark:text-white">{{ department.session.name }}</span>
                        </div>

                        <div class="p-2">
                            LEVEL: <span class="font-bold text-black dark:text-white">{{ department.level.name }}</span>
                        </div>
                    </div>

                    <Students
                        v-if="hasSummary"
                        :students="department.students" />

                    <EmptyState
                        v-else
                        description="Get started by downloading students from the Portal"
                        title="No Summary">
                        <IconLink :href="route('download.students.page')">Download Students</IconLink>
                    </EmptyState>
                </div>
            </Card>
        </AppPage>
    </AppLayout>
</template>
