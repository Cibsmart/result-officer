<script lang="ts" setup>
import { Head, useForm } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import { BreadcrumbItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import BaseFormSection from "@/components/baseFormSection.vue";
import { usePoll } from "@/composables/usePoll";
import { computed, watch } from "vue";
import SecondaryLink from "@/components/links/secondaryLink.vue";
import PrimaryLink from "@/components/links/primaryLink.vue";
import StaticFeeds from "@/components/feeds/staticFeeds.vue";
import ActiveFeeds from "@/components/feeds/activeFeeds.vue";

const props = defineProps<{
  events: Array<App.Data.Import.ImportEventData>;
  pending: App.Data.Import.PendingImportEventData;
}>();

const hasPendingEvent = computed(() => props.pending !== null);

const { start, stop } = usePoll(hasPendingEvent, ["pending", "events"]);

watch(hasPendingEvent, () => {
  if (hasPendingEvent.value === true) {
    start();
  } else {
    stop();
  }
});

const hasEvent = computed(() => props.events.length > 0);
const disableButton = computed(() => form.processing || hasPendingEvent.value);

const pages: BreadcrumbItem[] = [
  {
    name: "Download Departments",
    href: route("download.departments.page"),
    current: route().current("download.departments.page"),
  },
];

const form = useForm({});

const submit = () => {
  form.post(route("download.departments.store"));
};
</script>

<template>
  <Head title="Download Department Records" />

  <Breadcrumb :pages="pages" />

  <BaseHeader>Download Department Records</BaseHeader>

  <BasePage>
    <BaseSection>
      <BaseFormSection
        description="Click to Download All Departments from the Portal"
        header="Download Departments">
        <form
          class="mt-6 space-y-6"
          @submit.prevent="submit">
          <div>
            <PrimaryButton :disabled="disableButton">Download</PrimaryButton>
          </div>
        </form>
      </BaseFormSection>
    </BaseSection>

    <template v-if="hasPendingEvent">
      <BaseSection>
        <BaseFormSection description="Pending Department Download">
          <ActiveFeeds :data="pending" />

          <SecondaryLink
            :href="route('import.event.cancel', { event: pending.id })"
            class="mt-6">
            Cancel
          </SecondaryLink>

          <PrimaryLink
            v-if="pending.canBeContinued"
            :href="route('import.event.continue', { event: pending.id })"
            class="ml-4 mt-4">
            Continue
          </PrimaryLink>
        </BaseFormSection>
      </BaseSection>
    </template>

    <template v-if="hasEvent">
      <BaseSection>
        <BaseFormSection description="Department Download History">
          <StaticFeeds
            :events="events"
            class="my-4" />
        </BaseFormSection>
      </BaseSection>
    </template>
  </BasePage>
</template>
