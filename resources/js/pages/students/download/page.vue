<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import RegistrationNumber from "@/pages/students/download/tabs/registrationNumber.vue";
import BaseSection from "@/components/baseSection.vue";
import { BreadcrumbItem, TabItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseTabs from "@/components/tabs/baseTabs.vue";
import DepartmentSession from "@/pages/students/download/tabs/departmentSession.vue";
import Session from "@/pages/students/download/tabs/session.vue";
import { TabPanel, TabPanels } from "@headlessui/vue";

defineProps<{
  department: App.Data.Department.DepartmentListData;
  session: App.Data.Session.SessionListData;
}>();

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
        <TabPanels class="mt-8">
          <TabPanel>
            <RegistrationNumber />
          </TabPanel>

          <TabPanel>
            <DepartmentSession
              :departments="department.departments"
              :sessions="session.sessions" />
          </TabPanel>

          <TabPanel>
            <Session :sessions="session.sessions" />
          </TabPanel>
        </TabPanels>
      </BaseTabs>
    </BaseSection>
  </BasePage>
</template>
