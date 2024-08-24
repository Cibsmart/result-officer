<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import CompositeForm from "@/pages/reports/composite/form/partials/form.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import { BreadcrumbItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";

defineProps<{
  semester: App.Data.Semester.SemesterListData;
  session: App.Data.Session.SessionListData;
  program: App.Data.Program.ProgramListData;
  level: App.Data.Level.LevelListData;
}>();

const pages: BreadcrumbItem[] = [
  { name: "Composite Sheet", href: route("composite.form"), current: route().current("composite.form") },
];
</script>

<template>
  <Head title="View Composite Sheet" />

  <Breadcrumb :pages="pages" />

  <BaseHeader> Department Composite Sheet</BaseHeader>

  <BasePage>
    <BaseSection>
      <CompositeForm
        :levels="level.levels"
        :programs="program.programs"
        :semesters="semester.semesters"
        :sessions="session.sessions" />
    </BaseSection>
  </BasePage>
</template>
