<script lang="ts" setup>
import SecondaryLinkSmall from "@/components/links/secondaryLinkSmall.vue";
import Badge from "@/components/badge.vue";
import BaseTD from "@/components/tables/baseTD.vue";

defineProps<{
  event: App.Data.Vetting.VettingEventGroupData;
}>();
</script>

<template>
  <BaseTD position="left">
    <div>
      <div>{{ event.title }}</div>

      <div class="text-xs text-gray-400">{{ event.department }}</div>
    </div>
  </BaseTD>

  <BaseTD>{{ event.numberOfStudents }}</BaseTD>

  <BaseTD position="left">
    <Badge
      :class="event.status !== 'completed' ? 'animate-pulse' : ''"
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
</template>
