<script lang="ts" setup>
import { computed } from "vue";
import { Head } from "@inertiajs/vue3";
import { BreadcrumbItem } from "@/types";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import Breadcrumb from "@/components/breadcrumb.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import GraduationForm from "@/pages/vetting/list/index/partials/form.vue";
import List from "@/pages/vetting/list/index/partials/list.vue";

const props = defineProps<{
  departments: App.Data.Department.DepartmentListData;
  data: App.Data.Vetting.VettingListData;
  steps: App.Data.Vetting.VettingStepListData;
}>();

const hasData = computed(() => props.data !== null);

const pages: BreadcrumbItem[] = [
  { name: "Vetting", href: route("vetting.index"), current: route().current("vetting.index") },
];
</script>

<template>
  <Head title="Vetting Page" />

  <Breadcrumb :pages="pages" />

  <BaseHeader> View Vetting List</BaseHeader>

  <BasePage>
    <BaseSection>
      <GraduationForm :departments="departments.departments" />
    </BaseSection>

    <BaseSection v-if="hasData">
      <List
        :data="data"
        :steps="steps" />
    </BaseSection>
  </BasePage>
</template>
