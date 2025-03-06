<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import BaseSection from '@/layouts/main/partials/baseSection.vue';
import BaseHeader from '@/layouts/main/partials/baseHeader.vue';
import Breadcrumb from '@/components/breadcrumb.vue';
import BasePage from '@/layouts/main/partials/basePage.vue';
import { BreadcrumbItem } from '@/types';
import VettingForm from '@/pages/vetting/index/partials/vettingForm.vue';
import EmptyState from '@/components/emptyState.vue';
import VettingEventList from '@/pages/vetting/index/partials/vettingEventList.vue';
import { computed } from 'vue';
import { PaginatedVettingEventGroupListData } from '@/types/paginate';

const props = defineProps<{
    paginated: PaginatedVettingEventGroupListData;
}>();

const pages: BreadcrumbItem[] = [
    { name: 'Vetting', href: route('vettingEvent.index'), current: route().current('vettingEvent.index') },
];

const hasEvent = computed(() => props.paginated.data.length > 0);
</script>

<template>
    <Head title="Vetting Page" />

    <Breadcrumb :pages="pages" />

    <BaseHeader> Vetting Page</BaseHeader>

    <BasePage>
        <BaseSection>
            <VettingForm />
        </BaseSection>

        <template v-if="hasEvent">
            <VettingEventList :paginated="paginated" />
        </template>

        <template v-else>
            <BaseSection>
                <EmptyState
                    description="Start by vetting list of possible graduands above"
                    title="No Vetting Found" />
            </BaseSection>
        </template>
    </BasePage>
</template>
