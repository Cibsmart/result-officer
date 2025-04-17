<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import { BreadcrumbItem } from '@/types';
import { computed } from 'vue';
import BaseLink from '@/components/links/BaseLink.vue';
import EmptyState from '@/components/emptyState.vue';
import IconLink from '@/components/links/IconLink.vue';
import ResultForm from '@/pages/results/index/partials/resultForm.vue';
import ResultSessionView from '@/pages/results/index/partials/resultSessionView.vue';
import AppPage from '@/components/AppPage.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card } from '@/components/ui/card';

const props = defineProps<{
    student: App.Data.Students.StudentBasicData;
    results: App.Data.Results.StudentResultData;
}>();

const hasData = computed(() => props.student !== null);
const hasResults = computed(() => props.results !== null && props.results.sessionEnrollments.length > 0);

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Students', href: route('students.index') }];
</script>

<template>
    <Head title="View Student Result" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AppPage
            description="View Student Result Details"
            title="Student Results">
            <Card class="p-6">
                <ResultForm />
            </Card>

            <Card
                v-if="hasData"
                class="p-6">
                <div class="">
                    <div class="sm:flex sm:items-center">
                        <div class="sm:flex-auto">
                            <h1 class="text-base leading-6 font-semibold text-gray-900 dark:text-white">
                                Student Results
                            </h1>

                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                This page shows all results for the selected student
                            </p>
                        </div>

                        <div
                            v-show="hasResults"
                            class="mt-4 flex space-x-4">
                            <BaseLink :href="route('results.print', { student: student })"> Print</BaseLink>
                        </div>
                    </div>

                    <div
                        class="mt-5 divide-y divide-solid divide-gray-300 ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:divide-gray-600 dark:ring-gray-600">
                        <div class="p-2">
                            NAME: <span class="font-bold text-black dark:text-white">{{ student.name }}</span>
                        </div>

                        <div class="p-2">
                            REGISTRATION NUMBER:
                            <span class="font-bold text-black dark:text-white">{{ student.registrationNumber }}</span>
                        </div>

                        <div class="p-2">
                            DEPARTMENT:
                            <span class="font-bold text-black dark:text-white">{{ student.department }}</span>
                        </div>
                    </div>

                    <div>
                        <template v-if="hasResults">
                            <ResultSessionView
                                v-for="session in results.sessionEnrollments"
                                :key="session.id"
                                :session="session" />
                        </template>

                        <EmptyState
                            v-else
                            description="Get started by downloading student's results from the Portal"
                            title="No Result">
                            <IconLink :href="route('download.results.page')">Download Results</IconLink>
                        </EmptyState>
                    </div>

                    <div
                        v-if="hasResults"
                        class="mt-2 flex flex-col p-2 text-center text-xl font-bold text-black uppercase lg:block dark:text-white">
                        <span>
                            Current Final CGPA:
                            <span>{{ results.formattedFCGPA }} </span>
                        </span>

                        <span class="hidden lg:inline"> (</span>

                        <span class=""> {{ results.degreeClass }}</span>

                        <span class="hidden lg:inline">)</span>
                    </div>
                </div>
            </Card>
        </AppPage>
    </AppLayout>
</template>
