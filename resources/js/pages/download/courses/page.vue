<script lang="ts" setup>
import { Head, useForm } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import { BreadcrumbItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import BaseFormSection from "@/components/baseFormSection.vue";
import StaticFeeds from "@/components/feeds/staticFeeds.vue";
import ActiveFeeds from "@/components/feeds/activeFeeds.vue";
import { computed, watch } from "vue";
import { usePoll } from "@/composables/usePoll";
import SecondaryLink from "@/components/links/secondaryLink.vue";
import PrimaryLink from "@/components/links/primaryLink.vue";

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

const pages: BreadcrumbItem[] = [
  {
    name: "Download Courses",
    href: route("download.courses.page"),
    current: route().current("download.courses.page"),
  },
];

const hasEvent = computed(() => props.events.length > 0);
const disableButton = computed(() => form.processing || hasPendingEvent.value);

const form = useForm({});

const submit = () => {
  form.post(route("download.courses.store"));
};
</script>

<template>
  <Head title="Download Courses Record" />

  <Breadcrumb :pages="pages" />

  <BaseHeader>Download Courses Record</BaseHeader>

  <BasePage>
    <BaseSection>
      <BaseFormSection
        description="Click to Download All Courses from the Portal"
        header="Download Courses">
        <form
          class="mt-6 space-y-6"
          @submit.prevent="submit">
          <div>
            <PrimaryButton
              :disabled="disableButton"
              tooltip
              >Download</PrimaryButton
            >
          </div>
        </form>
      </BaseFormSection>
    </BaseSection>

    <template v-if="hasPendingEvent">
      <BaseSection>
        <BaseFormSection description="Pending Course Download">
          <ActiveFeeds :data="pending" />

          <SecondaryLink
            :href="route('download.courses.cancel', { event: pending.id })"
            class="mt-6">
            Cancel
          </SecondaryLink>

          <PrimaryLink
            v-if="pending.canBeContinued"
            :href="route('download.courses.continue', { event: pending.id })"
            class="ml-4 mt-4">
            Continue
          </PrimaryLink>
        </BaseFormSection>
      </BaseSection>
    </template>

    <template v-if="hasEvent">
      <BaseSection>
        <BaseFormSection description="Course Download History">
          <StaticFeeds
            :events="events"
            class="my-4" />
        </BaseFormSection>
      </BaseSection>
    </template>
  </BasePage>
</template>
