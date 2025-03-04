<script lang="ts" setup>
import { Deferred, Head } from "@inertiajs/vue3";
import BasePage from "@/layouts/main/partials/basePage.vue";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import { BreadcrumbItem } from "@/types";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import Badge from "@/components/badge.vue";
import BaseTable from "@/components/tables/baseTable.vue";
import BaseTHead from "@/components/tables/baseTHead.vue";
import BaseTH from "@/components/tables/baseTH.vue";
import BaseTBody from "@/components/tables/baseTBody.vue";
import BaseTR from "@/components/tables/baseTR.vue";
import BaseTD from "@/components/tables/baseTD.vue";
import PrimaryButtonSmall from "@/components/buttons/primaryButtonSmall.vue";
import SecondaryButtonSmall from "@/components/buttons/secondaryButtonSmall.vue";
import SecondaryLinkSmall from "@/components/links/secondaryLinkSmall.vue";
import VettingDetailDrawer from "@/pages/vetting/show/partials/vettingDetailDrawer.vue";
import { ref } from "vue";
import BaseDisclosure from "@/components/baseDisclosure.vue";

const props = defineProps<{
  event: App.Data.Vetting.VettingEventGroupData;
  data: App.Data.Vetting.VettingEventGroupDetailData;
}>();

const pages: BreadcrumbItem[] = [
  { name: "Vetting Page", href: route("vettingEvent.index"), current: route().current("vettingEvent.index") },
  { name: "Vetting Details", href: "#", current: route().current("vettingEvent.show") },
];

const handleClick = (student: string) => {
  currentStudent.value = student;
  openDrawer.value = true;
};

const currentStudent = ref("");
const openDrawer = ref(false);
</script>

<template>
  <Head title="Vetting Details Page" />

  <Breadcrumb :pages="pages" />

  <BaseHeader> View Vetting Details</BaseHeader>

  <BasePage>
    <BaseSection>
      <div class="mb-4 flex justify-between align-baseline">
        <header class="text-2xl font-bold">
          {{ `Vetting Details for (${props.event.title}) - ${props.event.department}` }}
        </header>

        <div>
          <Badge :color="props.event.statusColor">{{ props.event.status }}</Badge>
        </div>
      </div>

      <Deferred data="data">
        <template #fallback> Loading...</template>

        <template
          v-for="group in data.groups"
          :key="group.id">
          <BaseDisclosure
            :title="group.curriculum.name"
            size="full">
            <BaseTable>
              <BaseTHead>
                <BaseTH position="left">Student Name</BaseTH>

                <BaseTH position="left">Student Status</BaseTH>

                <BaseTH position="left">Registration Number</BaseTH>

                <BaseTH position="left">Vetting Status</BaseTH>

                <BaseTH position="right">Actions</BaseTH>
              </BaseTHead>

              <BaseTBody>
                <BaseTR
                  v-for="vetting in group.vettings"
                  :key="vetting.id">
                  <BaseTD position="left">{{ vetting.student.name }}</BaseTD>

                  <BaseTD position="left">
                    <Badge :color="vetting.student.statusColor"> {{ vetting.student.status }}</Badge>
                  </BaseTD>

                  <BaseTD position="left">{{ vetting.student.registrationNumber }}</BaseTD>

                  <BaseTD position="left">
                    <Badge
                      :class="event.status === 'vetting' ? 'animate-pulse' : ''"
                      :color="vetting.statusColor">
                      {{ vetting.status }}
                    </Badge>
                  </BaseTD>

                  <BaseTD position="right">
                    <PrimaryButtonSmall @click="handleClick(vetting.student.slug)">View</PrimaryButtonSmall>

                    <SecondaryButtonSmall
                      v-if="vetting.status === 'passed'"
                      class="ml-2">
                      Clear
                    </SecondaryButtonSmall>

                    <SecondaryLinkSmall
                      v-if="vetting.status === 'failed'"
                      :href="route('vetting.create', { student: vetting.student.slug })"
                      class="ml-2">
                      Re-vet
                    </SecondaryLinkSmall>
                  </BaseTD>
                </BaseTR>
              </BaseTBody>
            </BaseTable>
          </BaseDisclosure>
        </template>
      </Deferred>
    </BaseSection>
  </BasePage>

  <VettingDetailDrawer
    :openReportDrawer="openDrawer"
    :slug="currentStudent"
    @close="openDrawer = false" />
</template>
