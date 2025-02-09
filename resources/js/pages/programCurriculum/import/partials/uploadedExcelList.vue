<script lang="ts" setup>
import BaseTR from "@/components/tables/baseTR.vue";
import SecondaryLinkSmall from "@/components/links/secondaryLinkSmall.vue";
import BaseTH from "@/components/tables/baseTH.vue";
import BaseTD from "@/components/tables/baseTD.vue";
import BaseTBody from "@/components/tables/baseTBody.vue";
import BaseTable from "@/components/tables/baseTable.vue";
import BaseTHead from "@/components/tables/baseTHead.vue";
import SecondaryButtonSmall from "@/components/buttons/secondaryButtonSmall.vue";
import Badge from "@/components/badge.vue";
import CardFooter from "@/components/cards/cardFooter.vue";
import Card from "@/components/cards/card.vue";
import Modal from "@/components/modal.vue";
import CardHeader from "@/components/cards/cardHeader.vue";
import { ref } from "vue";

defineProps<{
  data: App.Data.Imports.ExcelImportEventListData;
}>();

const showFailedMessage = (event: App.Data.Imports.ExcelImportEventData) => {
  currentEvent.value = event;
  showModal.value = true;
};

const closeModal = () => (showModal.value = false);

const showModal = ref(false);
const currentEvent = ref(null);
</script>

<template>
  <BaseTable>
    <BaseTHead>
      <BaseTH position="left">FileName</BaseTH>

      <BaseTH position="left">Status</BaseTH>

      <BaseTH>Actions</BaseTH>
    </BaseTHead>

    <BaseTBody>
      <BaseTR
        v-for="event in data.events"
        :key="event.id">
        <BaseTD position="left">{{ event.fileName }}</BaseTD>

        <BaseTD position="left">
          <Badge :color="event.statusColor">{{ event.status }}</Badge>
        </BaseTD>

        <BaseTD>
          <SecondaryButtonSmall
            v-if="event.status === 'failed'"
            class="mr-2"
            preserveScroll="true"
            @click="showFailedMessage(event)">
            View
          </SecondaryButtonSmall>

          <SecondaryLinkSmall
            v-if="event.status !== 'completed'"
            :href="route('import.curriculum.delete', { event: event.id })"
            method="post"
            preserveScroll="true">
            Delete
          </SecondaryLinkSmall>
        </BaseTD>
      </BaseTR>
    </BaseTBody>
  </BaseTable>

  <Modal
    :show="showModal"
    @close="closeModal">
    <div class="p-6">
      <CardHeader>Failed Message For: {{ currentEvent.fileName }}</CardHeader>

      <Card>
        <span class="text-base text-gray-700 dark:text-gray-300">{{ currentEvent.message }}</span>
      </Card>

      <CardFooter>
        <span class="text-sm font-semibold text-gray-400">
          Correct the issues in the Excel file, delete the import and re-upload
        </span>
      </CardFooter>
    </div>
  </Modal>
</template>
