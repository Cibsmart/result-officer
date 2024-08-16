<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseSection from "@/components/baseSection.vue";
import BaseTabs from "@/components/tabs/baseTabs.vue";
import { BreadcrumbItem, TabItem } from "@/types";
import useDepartments from "@/composables/useDepartments";
import useSessions from "@/composables/useSessions";
import useSemesters from "@/composables/useSemesters";
import useCourses from "@/composables/useCourses";
import useLevels from "@/composables/useLevels";
import RegistrationNumber from "@/pages/download/results/tabs/registrationNumber.vue";
import DepartmentSessionLevel from "@/pages/download/results/tabs/departmentSessionLevel.vue";
import DepartmentSessionSemester from "@/pages/download/results/tabs/departmentSessionSemester.vue";
import SessionCourse from "@/pages/download/results/tabs/sessionCourse.vue";

const props = defineProps<{
  departments: App.Data.Department.DepartmentListData;
  sessions: App.Data.Session.SessionListData;
  semesters: App.Data.Semester.SemesterListData;
  courses: App.Data.Course.CourseListData;
  levels: App.Data.Level.LevelListData;
}>();

useDepartments.setDepartments(props.departments.departments);
useSessions.setSessions(props.sessions.sessions);
useSemesters.setSemesters(props.semesters.semesters);
useCourses.setCourses(props.courses.courses);
useLevels.setLevels(props.levels.levels);

useSessions.setSessions(props.sessions.sessions);

const pages: BreadcrumbItem[] = [
  {
    name: "Results Download",
    href: route("download.results.page"),
    current: route().current("download.results.page"),
  },
];

const tabs: TabItem[] = [
  { name: "By Registration Number", component: RegistrationNumber },
  { name: "By Department Session and Level", component: DepartmentSessionLevel },
  { name: "By Department Session and Semester", component: DepartmentSessionSemester },
  { name: "By Session and Course", component: SessionCourse },
];
</script>

<template>
  <Head title="Download Result Records" />

  <Breadcrumb :pages="pages" />

  <BaseHeader>Download Result Records</BaseHeader>

  <BasePage>
    <BaseSection>
      <BaseTabs :tabs="tabs" />
    </BaseSection>
  </BasePage>
</template>
