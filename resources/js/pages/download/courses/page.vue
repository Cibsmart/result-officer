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
import { computed, onMounted, onBeforeUnmount, watch } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps<{
  events: Array<App.Data.Import.ImportEventData>;
  pending: App.Data.Import.PendingImportEventData;
}>();

const checkPendingEvent = () => router.reload({ only: ["pending", "events"], replace: true });

let interval: ReturnType<typeof setInterval>;

const hasPendingEvent = computed(() => props.pending !== null);

const startPolling = () => (interval = setInterval(() => checkPendingEvent(), 1000));

const stopPolling = () => clearInterval(interval);

onMounted(() => hasPendingEvent.value && startPolling());

onBeforeUnmount(() => stopPolling());

watch(hasPendingEvent, () => {
  if (hasPendingEvent.value === true) {
    startPolling();
  } else {
    stopPolling();
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
            <PrimaryButton :disabled="form.processing">Download</PrimaryButton>
          </div>
        </form>
      </BaseFormSection>
    </BaseSection>

    <template v-if="hasPendingEvent">
      <BaseSection class="transition-2s transition-all ease-in-out">
        <BaseFormSection description="Pending Course Download">
          <ActiveFeeds :data="pending" />
        </BaseFormSection>
      </BaseSection>
    </template>

    <template v-if="hasEvent">
      <BaseSection>
        <BaseFormSection description="Course Download History">
          <StaticFeeds
            :events="events"
            class="mt-6" />
        </BaseFormSection>
      </BaseSection>
    </template>
  </BasePage>
</template>
