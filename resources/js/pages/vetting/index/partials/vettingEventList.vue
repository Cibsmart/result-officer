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
import Card from "@/components/cards/card.vue";
import Pagination from "@/components/pagination.vue";
import CardFooter from "@/components/cards/cardFooter.vue";

defineProps<{
  paginated: PaginatedVettingEventGroupListData;
}>();
</script>

<template>
  <Card>
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
          <BaseTD position="left">
            <div>
              <div>{{ event.title }}</div>

              <div class="text-xs text-gray-400">{{ event.department }}</div>
            </div>
          </BaseTD>

          <BaseTD>{{ event.numberOfStudents }}</BaseTD>

          <BaseTD position="left">
            <Badge
              :class="event.status !== 'passed' && event.status !== 'failed' ? 'animate-pulse' : ''"
              :color="event.statusColor">
              {{ event.status }}
            </Badge>
          </BaseTD>

          <BaseTD position="left">{{ event.date }}</BaseTD>

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

    <CardFooter>
      <Pagination :paginated="paginated" />
    </CardFooter>
  </Card>
</template>
