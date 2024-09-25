<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import RegistrationNumber from "@/pages/download/students/tabs/registrationNumber.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import { BreadcrumbItem, TabItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseTabs from "@/components/tabs/baseTabs.vue";
import DepartmentSession from "@/pages/download/students/tabs/departmentSession.vue";
import Session from "@/pages/download/students/tabs/session.vue";
import BaseTabPanel from "@/components/tabs/baseTabPanel.vue";
import BaseFormSection from "@/components/baseFormSection.vue";
import SecondaryLink from "@/components/links/secondaryLink.vue";
import StaticFeeds from "@/components/feeds/staticFeeds.vue";
import ActiveFeeds from "@/components/feeds/activeFeeds.vue";
import PrimaryLink from "@/components/links/primaryLink.vue";
import { usePoll } from "@/composables/usePoll";
import { computed, watch } from "vue";

const props = defineProps<{
  department: App.Data.Department.DepartmentListData;
  session: App.Data.Session.SessionListData;
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

const pages: BreadcrumbItem[] = [
  {
    name: "Student Download",
    href: route("download.students.page"),
    current: route().current("download.students.page"),
  },
];

const tabs: TabItem[] = [
  { name: "By Registration Number" },
  { name: "By Department and Session" },
  { name: "By Session" },
];
</script>

<template>
  <Head title="Download Student Record" />

  <Breadcrumb :pages="pages" />

  <BaseHeader>Download Student Record</BaseHeader>

  <BasePage>
    <BaseSection>
      <BaseTabs :tabs="tabs">
        <BaseTabPanel>
          <RegistrationNumber />
        </BaseTabPanel>

        <BaseTabPanel>
          <DepartmentSession
            :departments="department.departments"
            :sessions="session.sessions" />
        </BaseTabPanel>

        <BaseTabPanel>
          <Session :sessions="session.sessions" />
        </BaseTabPanel>
      </BaseTabs>
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
