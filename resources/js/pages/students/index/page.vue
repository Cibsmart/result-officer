<script lang="ts" setup>
import { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { PaginatedStudentListData } from '@/types/paginate';
import Pagination from '@/components/pagination.vue';
import BaseTable from '@/components/tables/baseTable.vue';
import BaseTHead from '@/components/tables/baseTHead.vue';
import BaseTH from '@/components/tables/baseTH.vue';
import BaseTBody from '@/components/tables/baseTBody.vue';
import BaseTR from '@/components/tables/baseTR.vue';
import BaseTD from '@/components/tables/baseTD.vue';
import Badge from '@/components/badge.vue';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import AppPage from '@/components/AppPage.vue';
import { Button } from '@/components/ui/button';

defineProps<{ paginated: PaginatedStudentListData }>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Students', href: route('students.index') }];
</script>

<template>
    <Head title="Students Page" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AppPage
            description="List of Students"
            title="Students">
            <Card>
                <CardContent>
                    <BaseTable>
                        <BaseTHead>
                            <BaseTH
                                mobile
                                position="left"
                                >NAME
                            </BaseTH>

                            <BaseTH position="left">REGISTRATION NUMBER</BaseTH>

                            <BaseTH>GENDER</BaseTH>

                            <BaseTH>STATUS</BaseTH>

                            <BaseTH position="left">DEPARTMENT</BaseTH>

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
                                    <Button
                                        variant="outline"
                                        asChild
                                        size="sm">
                                        <Link :href="route('students.show', { student: student.slug })"> View</Link>
                                    </Button>
                                </BaseTD>
                            </BaseTR>
                        </BaseTBody>
                    </BaseTable>
                </CardContent>

                <CardFooter>
                    <Pagination :paginated="paginated" />
                </CardFooter>
            </Card>
        </AppPage>
    </AppLayout>
</template>
