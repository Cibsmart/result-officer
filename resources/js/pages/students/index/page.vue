<script lang="ts" setup>
import AppPage from '@/components/AppPage.vue';
import Badge from '@/components/badge.vue';
import EmptyState from '@/components/emptyState.vue';
import { SecondaryLinkSmall } from '@/components/links';
import Pagination from '@/components/pagination.vue';
import { BaseTable, BaseTBody, BaseTD, BaseTH, BaseTHead, BaseTR, SortableTH } from '@/components/tables';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import { useStudentIndexQuery } from '@/composables/useStudentIndexQuery';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { PaginatedStudentListData } from '@/types/paginate';
import { Head } from '@inertiajs/vue3';
import StudentFilters from './partials/studentFilters.vue';

const props = defineProps<{
    paginated: PaginatedStudentListData;
    filters: App.Data.Students.StudentIndexFilterData;
    options: App.Data.Students.StudentFilterOptionsData;
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Students', href: route('students.index') }];

const { sortHref } = useStudentIndexQuery(() => props.filters);
</script>

<template>
    <Head title="Students Page" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AppPage
            description="List of Students"
            title="Students">
            <Card>
                <CardContent class="space-y-6">
                    <StudentFilters
                        :filters="filters"
                        :options="options" />

                    <EmptyState
                        v-if="paginated.data.length === 0"
                        description="No student matched this search. Try a different name, registration number or filter."
                        title="No students found" />

                    <BaseTable v-else>
                        <BaseTHead>
                            <SortableTH
                                :active="filters.sort === 'name'"
                                :direction="filters.direction"
                                :href="sortHref('name')"
                                >NAME
                            </SortableTH>

                            <SortableTH
                                :active="filters.sort === 'registration_number'"
                                :direction="filters.direction"
                                :href="sortHref('registration_number')"
                                >REGISTRATION NUMBER
                            </SortableTH>

                            <BaseTH>GENDER</BaseTH>

                            <BaseTH>STATUS</BaseTH>

                            <SortableTH
                                :active="filters.sort === 'department'"
                                :direction="filters.direction"
                                :href="sortHref('department')"
                                >DEPARTMENT
                            </SortableTH>

                            <BaseTH mobile>ACTION</BaseTH>
                        </BaseTHead>

                        <BaseTBody>
                            <BaseTR
                                v-for="student in paginated.data"
                                :key="student.id">
                                <BaseTD
                                    mobile
                                    position="left">
                                    {{ student.name }}

                                    <div class="mt-1 flex flex-col text-gray-700 sm:block lg:hidden dark:text-gray-300">
                                        <span>{{ student.registrationNumber }}</span>

                                        <span class="hidden sm:inline"> || </span>

                                        <span>
                                            <Badge :color="student.statusColor">{{ student.status }}</Badge>
                                        </span>

                                        <span class="hidden sm:inline"> || </span>

                                        <span>{{ student.department }}</span>
                                    </div>
                                </BaseTD>

                                <BaseTD position="left">{{ student.registrationNumber }}</BaseTD>

                                <BaseTD>{{ student.gender }}</BaseTD>

                                <BaseTD>
                                    <Badge :color="student.statusColor">{{ student.status }}</Badge>
                                </BaseTD>

                                <BaseTD
                                    class="w-80"
                                    position="left"
                                    >{{ student.department }}
                                </BaseTD>

                                <BaseTD
                                    class="px-2"
                                    mobile>
                                    <SecondaryLinkSmall :href="route('students.show', { student: student.slug })"
                                        >View
                                    </SecondaryLinkSmall>
                                </BaseTD>
                            </BaseTR>
                        </BaseTBody>
                    </BaseTable>
                </CardContent>

                <CardFooter v-if="paginated.data.length > 0">
                    <Pagination :paginated="paginated" />
                </CardFooter>
            </Card>
        </AppPage>
    </AppLayout>
</template>
