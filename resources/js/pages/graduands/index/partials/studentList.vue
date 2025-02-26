<script lang="ts" setup>
import { ref, computed } from "vue";
import EmptyState from "@/components/emptyState.vue";
import IconLink from "@/components/links/iconLink.vue";
import StudentRow from "@/pages/graduands/index/partials/studentRow.vue";
import Drawer from "@/components/drawer.vue";
import Disclosure from "@/components/baseDisclosure.vue";
import Card from "@/components/cards/card.vue";
import CardHeading from "@/components/cards/cardHeader.vue";
import Modal from "@/components/modal.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import BaseTable from "@/components/tables/baseTable.vue";
import BaseTHead from "@/components/tables/baseTHead.vue";
import BaseTH from "@/components/tables/baseTH.vue";
import BaseTBody from "@/components/tables/baseTBody.vue";
import BaseTR from "@/components/tables/baseTR.vue";
import { PaginatedVettingListData } from "@/types/paginate";
import CardFooter from "@/components/cards/cardFooter.vue";
import Pagination from "@/components/pagination.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import ClearanceConfirmationForm from "@/pages/graduands/index/partials/clearanceConfirmationForm.vue";

const props = defineProps<{
  department: App.Data.Department.DepartmentInfoData;
  steps: App.Data.Vetting.VettingStepListData;
  paginated: PaginatedVettingListData;
}>();

const showReport = ref(false);

const registrationNumber = ref("");

const hasRows = computed(() => props.paginated.data.length > 0);

const form = useForm({ student: "" });

const closeDrawer = () => (showReport.value = false);
const openDrawer = (student: App.Data.Vetting.VettingStudentData) => {
  registrationNumber.value = student.registrationNumber;

  form.student = student.slug;

  form.get(usePage().url, { only: ["steps"], preserveScroll: true, preserveState: true });

  showReport.value = true;
};

const openClearanceForm = ref(false);
const clearanceStudent = ref<App.Data.Vetting.VettingStudentData>();

const confirmStudentClearance = (student: App.Data.Vetting.VettingStudentData) => {
  clearanceStudent.value = student;
  openClearanceForm.value = true;
};

const closeModal = () => (openClearanceForm.value = false);
</script>

<template>
  <Card>
    <CardHeading>
      <div
        class="mt-1 divide-y divide-solid divide-gray-300 ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:divide-gray-600 dark:ring-gray-600">
        <div class="grid grid-flow-col">
          <div class="p-2">
            FACULTY: <span class="font-bold text-black dark:text-white">{{ department.faculty.name }}</span>
          </div>
        </div>

        <div class="grid grid-flow-col">
          <div class="p-2">
            DEPARTMENT: <span class="font-bold text-black dark:text-white">{{ department.department.name }}</span>
          </div>
        </div>
      </div>
    </CardHeading>

    <div>
      <template v-if="hasRows">
        <BaseTable>
          <BaseTHead>
            <BaseTH
              mobile
              position="left">
              NAME
            </BaseTH>

            <BaseTH position="left"> REGISTRATION NUMBER</BaseTH>

            <BaseTH position="left"> STATUS</BaseTH>

            <BaseTH position="left"> REPORT</BaseTH>

            <BaseTH
              mobile
              position="left">
              ACTIONS
            </BaseTH>
          </BaseTHead>

          <BaseTBody>
            <BaseTR
              v-for="student in paginated.data"
              :key="student.id">
              <StudentRow
                :student="student"
                @show-report="openDrawer"
                @show-clearance="confirmStudentClearance" />
            </BaseTR>
          </BaseTBody>
        </BaseTable>
      </template>

      <EmptyState
        v-else
        description="Get started by downloading students from the Portal"
        title="No Final Year Student">
        <IconLink :href="route('download.students.page')">Download Students</IconLink>
      </EmptyState>
    </div>

    <CardFooter class="mt-4">
      <Pagination :paginated="paginated" />
    </CardFooter>
  </Card>

  <Modal
    :show="openClearanceForm"
    @close="closeModal">
    <BaseSection v-if="clearanceStudent">
      <ClearanceConfirmationForm
        :student="clearanceStudent"
        @close="closeModal" />
    </BaseSection>
  </Modal>

  <Drawer
    :show="showReport"
    :title="registrationNumber"
    size="medium"
    sub="Vetting Report"
    @close="closeDrawer">
    <div
      v-for="vettingStep in steps?.items"
      :key="vettingStep.id">
      <Disclosure
        :badge="vettingStep.status"
        :color="vettingStep.color"
        :title="vettingStep.title"
        class="mt-2">
        <Card>
          <CardHeading>{{ vettingStep.description }}</CardHeading>

          <ul
            class="list-inside list-decimal divide-y divide-gray-300 dark:divide-gray-700"
            role="list">
            <li
              v-for="report in vettingStep.reports"
              :key="report.id"
              class="p-2">
              {{ report.message }}
            </li>
          </ul>
        </Card>
      </Disclosure>
    </div>
  </Drawer>
</template>
