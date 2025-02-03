<script lang="ts" setup>
import {ref, computed} from "vue";
import EmptyState from "@/components/emptyState.vue";
import IconLink from "@/components/links/iconLink.vue";
import StudentRow from "@/pages/vetting/list/index/partials/studentRow.vue";
import Drawer from "@/components/drawer.vue";
import Disclosure from "@/components/baseDisclosure.vue";
import Card from "@/components/cards/card.vue";
import CardHeading from "@/components/cards/cardHeader.vue";
import Modal from "@/components/modal.vue";
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import SecondaryButton from "@/components/buttons/secondaryButton.vue";
import {useForm, usePage} from "@inertiajs/vue3";
import BaseTable from "@/components/tables/baseTable.vue";
import BaseTHead from "@/components/tables/baseTHead.vue";
import BaseTH from "@/components/tables/baseTH.vue";
import BaseTBody from "@/components/tables/baseTBody.vue";
import BaseTR from "@/components/tables/baseTR.vue";

const props = defineProps<{
  data: App.Data.Vetting.VettingListData;
  steps: App.Data.Vetting.VettingStepListData;
}>();

const showReport = ref(false);
const confirmingStudentClearance = ref(false);
const clearanceStudent = ref<App.Data.Vetting.VettingStudentData>();
const registrationNumber = ref("");

const hasRows = computed(() => props.data.graduands.length > 0);

const clearForm = useForm({});
const viewForm = useForm({student: ""});

const closeDrawer = () => (showReport.value = false);
const openDrawer = (student: App.Data.Vetting.VettingStudentData) => {
  registrationNumber.value = student.registrationNumber;

  viewForm.student = student.slug;

  viewForm.get(usePage().url, {only: ["steps"], preserveScroll: true, preserveState: true});

  showReport.value = true;
};

const closeModal = () => (confirmingStudentClearance.value = false);
const confirmStudentClearance = (student: App.Data.Vetting.VettingStudentData) => {
  clearanceStudent.value = student;
  confirmingStudentClearance.value = true;
};
const clearStudent = () => {
  clearForm.post(route("students.clearance.store", {student: clearanceStudent.value?.id}), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
  });
};
</script>

<template>
  <div>
    <div
        class="mt-1 divide-y divide-solid divide-gray-300 ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:divide-gray-600 dark:ring-gray-600">
      <div class="grid grid-flow-col">
        <div class="p-2">
          FACULTY: <span class="font-bold text-black dark:text-white">{{ data.faculty.name }}</span>
        </div>
      </div>

      <div class="grid grid-flow-col">
        <div class="p-2">
          DEPARTMENT: <span class="font-bold text-black dark:text-white">{{ data.department.name }}</span>
        </div>
      </div>
    </div>

    <div>
      <template v-if="hasRows">
        <BaseTable>
          <BaseTHead>
            <BaseTH>SN</BaseTH>

            <BaseTH
                mobile
                position="left">
              NAME
            </BaseTH>

            <BaseTH position="left"> REGISTRATION NUMBER</BaseTH>

            <BaseTH position="left"> STATUS</BaseTH>

            <BaseTH position="left"> REPORT</BaseTH>

            <BaseTH mobile position="left"> ACTIONS</BaseTH>
          </BaseTHead>

          <BaseTBody>
            <BaseTR
                v-for="(student, index) in data.graduands"
                :key="student.id">
              <StudentRow
                  :index="index"
                  :student="student"
                  @show-report="openDrawer"
                  @show-clearance="confirmStudentClearance"/>
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
  </div>

  <Modal
      :show="confirmingStudentClearance"
      @close="closeModal">
    <div class="p-6">
      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Clearance Confirmation</h2>

      <p class="mt-1 text-base text-gray-600 dark:text-gray-300">
        You are confirming that
        <span class="font-bold">{{ clearanceStudent?.name }}</span>
        with Registration Number
        <span class="font-bold">{{ clearanceStudent?.registrationNumber }}</span>
        has satisfy
        <span class="font-bold">ALL</span> academic requirement for graduation.
      </p>

      <div class="mt-6 flex justify-end">
        <SecondaryButton @click="closeModal"> Cancel</SecondaryButton>

        <PrimaryButton
            :class="{ 'opacity-25': clearForm.processing }"
            :disabled="clearForm.processing"
            class="ms-3"
            @click="clearStudent">
          Clear Student
        </PrimaryButton>
      </div>
    </div>
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
