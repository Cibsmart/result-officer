<script lang="ts" setup>
import BaseTR from "@/components/tables/baseTR.vue";
import SecondaryLinkSmall from "@/components/links/secondaryLinkSmall.vue";
import BaseTH from "@/components/tables/baseTH.vue";
import BaseTD from "@/components/tables/baseTD.vue";
import BaseTBody from "@/components/tables/baseTBody.vue";
import BaseTable from "@/components/tables/baseTable.vue";
import BaseTHead from "@/components/tables/baseTHead.vue";
import Badge from "@/components/badge.vue";
import { PaginatedVettingEventGroupListData } from "@/types/paginate";

defineProps<{
  paginated: PaginatedVettingEventGroupListData;
}>();
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
        v-for="event in paginated.data"
        :key="event.id">
        <BaseTD position="left">{{ event.title }}</BaseTD>

        <BaseTD position="left">
          <Badge
            :class="event.status !== 'passed' && event.status !== 'failed' ? 'animate-pulse' : ''"
            :color="event.statusColor">
            {{ event.status }}
          </Badge>
        </BaseTD>

        <BaseTD>
          <SecondaryLinkSmall
            v-if="event.status === 'queued' || event.status === 'failed'"
            :href="route('import.curriculum.delete', { event: event.id })"
            as="button"
            method="post"
            preserveScroll="true">
            Delete
          </SecondaryLinkSmall>
        </BaseTD>
      </BaseTR>
    </BaseTBody>
  </BaseTable>
</template>
