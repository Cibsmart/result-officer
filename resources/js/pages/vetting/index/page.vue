<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import { BreadcrumbItem } from '@/types';
import VettingForm from '@/pages/vetting/index/partials/vettingForm.vue';
import EmptyState from '@/components/emptyState.vue';
import VettingEventList from '@/pages/vetting/index/partials/vettingEventList.vue';
import { computed } from 'vue';
import { PaginatedVettingEventGroupListData } from '@/types/paginate';
import AppPage from '@/components/AppPage.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card } from '@/components/ui/card';

const props = defineProps<{
    paginated: PaginatedVettingEventGroupListData;
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Vetting', href: route('vettingEvent.index') }];

const hasEvent = computed(() => props.paginated.data.length > 0);
</script>

<template>
    <Head title="Vetting Page" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AppPage
            description="Vet Result of Possible Graduands"
            title="Vetting">
            <Card class="p-6">
                <VettingForm />
            </Card>

            <template v-if="hasEvent">
                <VettingEventList :paginated="paginated" />
            </template>

            <template v-else>
                <Card>
                    <EmptyState
                        description="Start by vetting list of possible graduands above"
                        title="No Vetting Found" />
                </Card>
            </template>
        </AppPage>
    </AppLayout>
</template>
