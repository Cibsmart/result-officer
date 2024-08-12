<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import RegistrationNumber from "@/pages/download/students/tabs/registrationNumber.vue";
import BaseSection from "@/components/baseSection.vue";
import { BreadcrumbItem, TabItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseTabs from "@/components/tabs/baseTabs.vue";
import DepartmentSession from "@/pages/download/students/tabs/departmentSession.vue";
import Session from "@/pages/download/students/tabs/session.vue";
import useDepartment from "@/composables/useDepartment.js";
import useSession from "@/composables/useSession.js";

const props = defineProps<{
  department: App.Data.Department.DepartmentListData;
  session: App.Data.Session.SessionListData;
}>();

useDepartment.setDepartments(props.department.departments);
useSession.setSessions(props.session.sessions);

const pages: BreadcrumbItem[] = [
  {
    name: "Student Download",
    href: route("download.students.page"),
    current: route().current("download.students.page"),
  },
];

const tabs: TabItem[] = [
  { name: "By Registration Number", component: RegistrationNumber },
  { name: "By Department and Session", component: DepartmentSession },
  { name: "By Session", component: Session },
];
</script>

<template>
  <Head title="Download Student Record" />

  <Breadcrumb :pages="pages" />

  <BaseHeader>Download Student Record</BaseHeader>

  <BasePage>
    <BaseSection>
      <BaseTabs :tabs="tabs" />
    </BaseSection>
  </BasePage>
</template>
