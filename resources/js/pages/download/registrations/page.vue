<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import { BreadcrumbItem, TabItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseTabs from "@/components/tabs/baseTabs.vue";
import RegistrationNumber from "@/pages/download/registrations/tabs/registrationNumber.vue";
import DepartmentSessionLevel from "@/pages/download/registrations/tabs/departmentSessionLevel.vue";
import DepartmentSessionSemester from "@/pages/download/registrations/tabs/departmentSessionSemester.vue";
import SessionCourse from "@/pages/download/registrations/tabs/sessionCourse.vue";
import BaseTabPanel from "@/components/tabs/baseTabPanel.vue";
import ImportEvents from "@/pages/download/components/importEvents.vue";

defineProps<{
  departments: App.Data.Department.DepartmentListData;
  sessions: App.Data.Session.SessionListData;
  semesters: App.Data.Semester.SemesterListData;
  courses: App.Data.Course.CourseListData;
  levels: App.Data.Level.LevelListData;
  events: Array<App.Data.Import.ImportEventData>;
  pending: App.Data.Import.PendingImportEventData;
  selectedIndex: number;
}>();

const pages: BreadcrumbItem[] = [
  {
    name: "Course Registration Download",
    href: route("download.registrations.page", { selectedIndex: 0 }),
    current: route().current("download.registrations.page"),
  },
];

const tabs: TabItem[] = [
  { name: "By Registration Number" },
  { name: "By Department Session and Level" },
  { name: "By Department Session and Semester" },
  { name: "By Session and Course" },
];
</script>

<template>
  <Head title="Download Course Registration Records" />

  <Breadcrumb :pages="pages" />

  <BaseHeader>Download Course Registration Records</BaseHeader>

  <BasePage>
    <BaseSection>
      <BaseTabs
        :selectedIndex="selectedIndex"
        :tabs="tabs">
        <BaseTabPanel>
          <RegistrationNumber />
        </BaseTabPanel>

        <BaseTabPanel>
          <DepartmentSessionLevel
            :departments="departments.data"
            :levels="levels.levels"
            :sessions="sessions.sessions" />
        </BaseTabPanel>

        <BaseTabPanel>
          <DepartmentSessionSemester
            :departments="departments.data"
            :semesters="semesters.semesters"
            :sessions="sessions.sessions" />
        </BaseTabPanel>

        <BaseTabPanel>
          <SessionCourse
            :courses="courses.courses"
            :sessions="sessions.sessions" />
        </BaseTabPanel>
      </BaseTabs>
    </BaseSection>

    <ImportEvents
      :events="events"
      :pending="pending" />
  </BasePage>
</template>
