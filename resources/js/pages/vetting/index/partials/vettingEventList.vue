<script lang="ts" setup>
import BaseTR from '@/components/tables/baseTR.vue';
import BaseTH from '@/components/tables/baseTH.vue';
import BaseTBody from '@/components/tables/baseTBody.vue';
import BaseTable from '@/components/tables/baseTable.vue';
import BaseTHead from '@/components/tables/baseTHead.vue';
import { PaginatedVettingEventGroupListData } from '@/types/paginate';
import Pagination from '@/components/pagination.vue';
import { computed, onMounted, watch } from 'vue';
import { usePoll } from '@inertiajs/vue3';
import VettingEventRow from '@/pages/vetting/index/partials/vettingEventRow.vue';
import { Card, CardContent, CardFooter } from '@/components/ui/card';

const props = defineProps<{
    paginated: PaginatedVettingEventGroupListData;
}>();

const { start, stop } = usePoll(5000, {}, { autoStart: false });
const hasUncompletedVetting = computed(() => props.paginated.data.some((group) => group.status !== 'completed'));

onMounted(() => {
    if (hasUncompletedVetting.value) {
        start();
    }
});

watch(hasUncompletedVetting, () => {
    if (hasUncompletedVetting.value) {
        start();
    } else {
        stop();
    }
});
</script>

<template>
    <Card>
        <CardContent>
            <BaseTable>
                <BaseTHead>
                    <BaseTH position="left">Title</BaseTH>

                    <BaseTH>Number of Students</BaseTH>

                    <BaseTH position="left">Status</BaseTH>

                    <BaseTH position="left">Date</BaseTH>

                    <BaseTH>Actions</BaseTH>
                </BaseTHead>

                <BaseTBody>
                    <BaseTR
                        v-for="event in paginated.data"
                        :key="event.id">
                        <VettingEventRow :event="event" />
                    </BaseTR>
                </BaseTBody>
            </BaseTable>
        </CardContent>

        <CardFooter>
            <Pagination :paginated="paginated" />
        </CardFooter>
    </Card>
</template>
