<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import ResultSummaryForm from "@/pages/summary/form/partials/form.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import { BreadcrumbItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";

defineProps<{
  department: App.Data.Department.DepartmentListData;
  session: App.Data.Session.SessionListData;
  level: App.Data.Level.LevelListData;
}>();

const pages: BreadcrumbItem[] = [
  { name: "Summary", href: route("summary.form"), current: route().current("summary.form") },
];
</script>

<template>
  <Head title="View Student Result" />

  <Breadcrumb :pages="pages" />

  <BaseHeader> Department Result Summary</BaseHeader>

  <BasePage>
    <BaseSection>
      <ResultSummaryForm
        :departments="department.departments"
        :levels="level.levels"
        :sessions="session.sessions"
        class="max-w-xl" />
    </BaseSection>
  </BasePage>
</template>
