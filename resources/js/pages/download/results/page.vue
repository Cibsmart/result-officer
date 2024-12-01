<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import { BreadcrumbItem, TabItem } from "@/types";
import RegistrationNumber from "@/pages/download/results/tabs/registrationNumber.vue";
import DepartmentSessionLevel from "@/pages/download/results/tabs/departmentSessionLevel.vue";
import DepartmentSessionSemester from "@/pages/download/results/tabs/departmentSessionSemester.vue";
import SessionCourse from "@/pages/download/results/tabs/sessionCourse.vue";
import BaseTabs from "@/components/tabs/baseTabs.vue";
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
}>();

const pages: BreadcrumbItem[] = [
  {
    name: "Results Download",
    href: route("download.results.page"),
    current: route().current("download.results.page"),
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
  <Head title="Download Result Records" />

  <Breadcrumb :pages="pages" />

  <BaseHeader>Download Result Records</BaseHeader>

  <BasePage>
    <BaseSection>
      <BaseTabs :tabs="tabs">
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
