<script lang="ts" setup>
import BaseFormSection from "@/components/forms/baseFormSection.vue";
import SecondaryLink from "@/components/links/secondaryLink.vue";
import StaticFeeds from "@/components/feeds/staticFeeds.vue";
import ActiveFeeds from "@/components/feeds/activeFeeds.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import PrimaryLink from "@/components/links/primaryLink.vue";
import { usePoll } from "@/composables/usePoll";
import { computed, watch } from "vue";

const props = defineProps<{
  events: Array<App.Data.Import.ImportEventData>;
  pending: App.Data.Import.PendingImportEventData;
}>();

const hasPendingEvent = computed(() => props.pending !== null);
const hasEvent = computed(() => props.events.length > 0);
const pendingDescription = computed(() => (hasPendingEvent.value ? `Pending ${props.pending.type} Download` : ""));
const historyDescription = computed(() => (hasEvent.value ? `Recent ${props.events[0].type} Download History` : ""));

const { start, stop } = usePoll(hasPendingEvent, ["pending", "events"]);

watch(hasPendingEvent, () => {
  if (hasPendingEvent.value === true) {
    start();
  } else {
    stop();
  }
});
</script>

<template>
  <template v-if="hasPendingEvent">
    <BaseSection>
      <BaseFormSection :description="pendingDescription">
        <ActiveFeeds :data="pending" />

        <SecondaryLink
          :href="route('import.event.cancel', { event: pending.id })"
          class="mt-6">
          Cancel
        </SecondaryLink>

        <PrimaryLink
          v-if="pending.canBeContinued"
          :href="route('import.event.continue', { event: pending.id })"
          class="mt-4 ml-4">
          Continue
        </PrimaryLink>
      </BaseFormSection>
    </BaseSection>
  </template>

  <template v-if="hasEvent">
    <BaseSection>
      <BaseFormSection :description="historyDescription">
        <StaticFeeds
          :events="events"
          class="my-4" />
      </BaseFormSection>
    </BaseSection>
  </template>
</template>
