<script lang="ts" setup>
import { BaseTable, BaseTBody, BaseTD, BaseTH, BaseTHead, BaseTR } from '@/components/tables';
import { SecondaryLinkSmall } from '@/components/links';
import { SecondaryButtonSmall } from '@/components/buttons';
import Badge from '@/components/badge.vue';
import Modal from '@/components/modal.vue';
import { ref } from 'vue';
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';

defineProps<{
    data: App.Data.Imports.ExcelImportEventListData;
}>();

const showFailedMessage = (event: App.Data.Imports.ExcelImportEventData) => {
    currentEvent.value = event;
    showModal.value = true;
};

const closeModal = () => (showModal.value = false);

const showModal = ref(false);
const currentEvent = ref<App.Data.Imports.ExcelImportEventData | null>(null);
</script>

<template>
    <BaseTable>
        <BaseTHead>
            <BaseTH position="left">File Name</BaseTH>

            <BaseTH position="left">Date Uploaded</BaseTH>

            <BaseTH position="left">Status</BaseTH>

            <BaseTH>Actions</BaseTH>
        </BaseTHead>

        <BaseTBody>
            <BaseTR
                v-for="event in data.events"
                :key="event.id">
                <BaseTD position="left">{{ event.fileName }}</BaseTD>

                <BaseTD position="left">{{ event.date }}</BaseTD>

                <BaseTD position="left">
                    <Badge
                        :class="event.status !== 'completed' && event.status !== 'failed' ? 'animate-pulse' : ''"
                        :color="event.statusColor">
                        {{ event.status }}
                    </Badge>
                </BaseTD>

                <BaseTD>
                    <div class="flex gap-2">
                        <SecondaryButtonSmall
                            v-if="event.status === 'failed'"
                            @click="showFailedMessage(event)">
                            View
                        </SecondaryButtonSmall>

                        <SecondaryLinkSmall
                            v-if="event.status === 'queued' || event.status === 'failed'"
                            :href="route('import.curriculum.delete', { event: event.id })"
                            as="button"
                            method="post"
                            preserveScroll>
                            Delete
                        </SecondaryLinkSmall>
                    </div>
                </BaseTD>
            </BaseTR>
        </BaseTBody>
    </BaseTable>

    <Modal
        :show="showModal"
        @close="closeModal">
        <Card class="p-6">
            <CardHeader>Failed Message For: {{ currentEvent?.fileName }}</CardHeader>

            <CardContent>
                <pre class="text-base whitespace-pre-wrap text-gray-700 dark:text-gray-300">{{
                    currentEvent?.message
                }}</pre>
            </CardContent>

            <CardFooter>
                <span class="text-sm font-semibold text-gray-400">
                    Correct the issues in the Excel file, delete the import and re-upload
                </span>
            </CardFooter>
        </Card>
    </Modal>
</template>
