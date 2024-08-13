<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BaseSection from "@/components/baseSection.vue";
import { BreadcrumbItem, TabItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseTabs from "@/components/tabs/baseTabs.vue";
import useDepartment from "@/composables/useDepartment.js";
import useSession from "@/composables/useSession.js";
import useLevel from "@/composables/useLevel";
import useCourse from "@/composables/useCourse";
import RegistrationNumber from "@/pages/download/registrations/tabs/registrationNumber.vue";
import DepartmentSessionLevel from "@/pages/download/registrations/tabs/departmentSessionLevel.vue";
import DepartmentSessionSemester from "@/pages/download/registrations/tabs/departmentSessionSemester.vue";
import SessionCourse from "@/pages/download/registrations/tabs/sessionCourse.vue";
import useSemester from "@/composables/useSemester";

const props = defineProps<{
  departments: App.Data.Department.DepartmentListData;
  sessions: App.Data.Session.SessionListData;
  semesters: App.Data.Semester.SemesterListDataa;
  courses: App.Data.Course.CourseListData;
  levels: App.Data.Level.LevelListData;
}>();

useDepartment.setDepartments(props.departments.departments);
useSession.setSessions(props.sessions.sessions);
useSemester.setSemesters(props.semesters.semesters);
useCourse.setCourses(props.courses.courses);
useLevel.setLevels(props.levels.levels);

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
