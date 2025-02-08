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
import ImportEvents from "@/pages/download/components/importEvents.vue";

defineProps<{
  department: App.Data.Department.DepartmentListData;
  session: App.Data.Session.SessionListData;
  events: Array<App.Data.Imports.ImportEventData>;
  pending: App.Data.Imports.PendingImportEventData;
  selectedIndex: number;
}>();

const pages: BreadcrumbItem[] = [
  {
    name: "Student Download",
    href: route("download.students.page", { selectedIndex: 0 }),
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
      <BaseTabs
        :selectedIndex="selectedIndex"
        :tabs="tabs">
        <BaseTabPanel>
          <RegistrationNumber />
        </BaseTabPanel>

        <BaseTabPanel>
          <DepartmentSession
            :departments="department.data"
            :sessions="session.sessions" />
        </BaseTabPanel>

        <BaseTabPanel>
          <Session :sessions="session.sessions" />
        </BaseTabPanel>
      </BaseTabs>
    </BaseSection>

    <ImportEvents
      :events="events"
      :pending="pending" />
  </BasePage>
</template>
