<script lang="ts" setup>
import { Deferred, Head } from '@inertiajs/vue3';
import { BreadcrumbItem } from '@/types';
import Badge from '@/components/badge.vue';
import BaseTable from '@/components/tables/baseTable.vue';
import BaseTHead from '@/components/tables/baseTHead.vue';
import BaseTH from '@/components/tables/baseTH.vue';
import BaseTBody from '@/components/tables/baseTBody.vue';
import BaseTR from '@/components/tables/baseTR.vue';
import BaseTD from '@/components/tables/baseTD.vue';
import VettingDetailDrawer from '@/pages/vetting/show/partials/vettingDetailDrawer.vue';
import { ref } from 'vue';
import BaseDisclosure from '@/components/baseDisclosure.vue';
import AppPage from '@/components/AppPage.vue';
import { Card } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { SecondaryLinkSmall } from '@/components/links';
import { PrimaryButtonSmall, SecondaryButtonSmall } from '@/components/buttons';

const props = defineProps<{
    event: App.Data.Vetting.VettingEventGroupData;
    data: App.Data.Vetting.VettingEventGroupDetailData;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Vetting Page', href: route('vettingEvent.index') },
    { title: 'Vetting Details', href: '#' },
];

const handleClick = (student: string) => {
    currentStudent.value = student;
    openDrawer.value = true;
};

const currentStudent = ref('');
const openDrawer = ref(false);
</script>

<template>
    <Head title="Vetting Details Page" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AppPage
            description="View Vetting Details"
            title="Vetting Details">
            <Card>
                <div class="mb-4 flex justify-between align-baseline">
                    <header class="text-2xl font-bold">
                        {{ `Vetting Details for (${props.event.title}) - ${props.event.department}` }}
                    </header>

                    <div class="flex items-center space-x-2">
                        <PrimaryButtonSmall>Report</PrimaryButtonSmall>

                        <div>
                            <Badge :color="props.event.statusColor">{{ props.event.status }}</Badge>
                        </div>
                    </div>
                </div>

                <Deferred data="data">
                    <template #fallback>
                        <div class="animate-pulse">
                            <div class="flex-1 space-y-6 py-1">
                                <div class="h-4 rounded bg-gray-200 dark:bg-gray-700" />

                                <div class="h-3 rounded bg-gray-200 dark:bg-gray-700" />

                                <div class="h-3 rounded bg-gray-200 dark:bg-gray-700" />

                                <div class="h-3 rounded bg-gray-200 dark:bg-gray-700" />
                            </div>
                        </div>
                    </template>

                    <template
                        v-for="group in data.groups"
                        :key="group.id">
                        <BaseDisclosure
                            defaultOpen
                            size="full">
                            <template #header>
                                <div class="flex flex-1 justify-between text-sm font-black">
                                    <span class="text-lg uppercase">Course List: {{ group.curriculum.name }}</span>

                                    <SecondaryButtonSmall
                                        size="sm"
                                        type="button"
                                        variant="secondary">
                                        View
                                    </SecondaryButtonSmall>
                                </div>
                            </template>

                            <BaseTable>
                                <BaseTHead>
                                    <BaseTH position="left">Student Name</BaseTH>

                                    <BaseTH position="left">Student Status</BaseTH>

                                    <BaseTH position="left">Registration Number</BaseTH>

                                    <BaseTH position="left">Vetting Status</BaseTH>

                                    <BaseTH position="right">Actions</BaseTH>
                                </BaseTHead>

                                <BaseTBody>
                                    <BaseTR
                                        v-for="vetting in group.vettings"
                                        :key="vetting.id">
                                        <BaseTD position="left">{{ vetting.student.name }}</BaseTD>

                                        <BaseTD position="left">
                                            <Badge :color="vetting.student.statusColor">
                                                {{ vetting.student.status }}
                                            </Badge>
                                        </BaseTD>

                                        <BaseTD position="left">{{ vetting.student.registrationNumber }}</BaseTD>

                                        <BaseTD position="left">
                                            <Badge
                                                :class="event.status === 'vetting' ? 'animate-pulse' : ''"
                                                :color="vetting.statusColor">
                                                {{ vetting.status }}
                                            </Badge>
                                        </BaseTD>

                                        <BaseTD position="right">
                                            <div class="flex gap-2">
                                                <PrimaryButtonSmall @click="handleClick(vetting.student.slug)">
                                                    View
                                                </PrimaryButtonSmall>

                                                <PrimaryButtonSmall
                                                    v-if="vetting.status === 'passed'"
                                                    size="sm"
                                                    variant="secondary"
                                                    class="ml-2">
                                                    Clear
                                                </PrimaryButtonSmall>

                                                <SecondaryLinkSmall
                                                    v-if="vetting.status === 'failed'"
                                                    :href="route('vetting.create', { student: vetting.student.slug })">
                                                    Re-vet
                                                </SecondaryLinkSmall>
                                            </div>
                                        </BaseTD>
                                    </BaseTR>
                                </BaseTBody>
                            </BaseTable>
                        </BaseDisclosure>
                    </template>
                </Deferred>
            </Card>
        </AppPage>

        <VettingDetailDrawer
            :openReportDrawer="openDrawer"
            :slug="currentStudent"
            @close="openDrawer = false" />
    </AppLayout>
</template>
