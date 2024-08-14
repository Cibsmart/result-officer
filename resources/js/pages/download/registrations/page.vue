<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BaseSection from "@/components/baseSection.vue";
import { BreadcrumbItem, TabItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseTabs from "@/components/tabs/baseTabs.vue";
import useDepartments from "@/composables/useDepartments";
import useSessions from "@/composables/useSessions";
import useLevels from "@/composables/useLevels";
import useCourses from "@/composables/useCourses";
import useSemesters from "@/composables/useSemesters";
import RegistrationNumber from "@/pages/download/registrations/tabs/registrationNumber.vue";
import DepartmentSessionLevel from "@/pages/download/registrations/tabs/departmentSessionLevel.vue";
import DepartmentSessionSemester from "@/pages/download/registrations/tabs/departmentSessionSemester.vue";
import SessionCourse from "@/pages/download/registrations/tabs/sessionCourse.vue";

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
    name: "Course Registration Download",
    href: route("download.course-registrations.page"),
    current: route().current("download.course-registrations.page"),
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
  <Head title="Download Course Registration Records" />

  <Breadcrumb :pages="pages" />

  <BaseHeader>Download Course Registration Records</BaseHeader>

  <BasePage>
    <BaseSection>
      <BaseTabs :tabs="tabs" />
    </BaseSection>
  </BasePage>
</template>
