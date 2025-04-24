<script lang="ts" setup>
import { PrimaryLinkSmall, SecondaryLinkSmall } from '@/components/links';
import Badge from '@/components/badge.vue';
import { BaseTD } from '@/components/tables';

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
        <PrimaryLinkSmall :href="route('vettingEvent.show', { vettingEvent: event.slug })"> View</PrimaryLinkSmall>

        <SecondaryLinkSmall
            v-if="event.status === 'queued'"
            :href="route('vettingEvent.destroy', { vettingEvent: event.slug })"
            as="button"
            class="ml-2"
            method="delete"
            preserveScroll="true">
            Delete
        </SecondaryLinkSmall>
    </BaseTD>
</template>
