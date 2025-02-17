<script lang="ts" setup>
import DataList from "@/components/data/dataList.vue";
import { ref, defineAsyncComponent } from "vue";
import DataItem from "@/components/data/dataItem.vue";
import SectionHeader from "@/components/sectionHeader.vue";
import SecondaryButtonSmall from "@/components/buttons/secondaryButtonSmall.vue";
import Modal from "@/components/modal.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";

defineProps<{
  student: App.Data.Students.StudentData;
}>();

const editField = ref<App.Enums.ModifiableFields.StudentModifiableField | "">("");
const showEditModal = ref(false);

const componentList: Record<
  App.Enums.ModifiableFields.StudentModifiableField,
  ReturnType<typeof defineAsyncComponent>
> = {
  registration_number: defineAsyncComponent(
    () => import("@/pages/students/show/partials/updates/registrationNumberUpdateForm.vue"),
  ),
  // exam: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/examUpdateForm.vue")),
  // name: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/nameUpdateForm.vue")),
  // course: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/courseUpdateForm.vue")),
  // gender: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/genderUpdateForm.vue")),
  // status: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/statusUpdateForm.vue")),
  // program: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/programUpdateForm.vue")),
  // in_course: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/inCourseUpdateForm.vue")),
  // entry_mode: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/entryModeUpdateForm.vue")),
  // credit_unit: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/creditUnitUpdateForm.vue")),
  // entry_level: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/entryLevelUpdateForm.vue")),
  // date_of_birth: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/dateOfBirthUpdateForm.vue")),
  // local_government: defineAsyncComponent(
  //   () => import("@/pages/students/show/partials/updates/localGovernmentUpdateForm.vue"),
  // ),
};

const openEditModal = (field: App.Enums.ModifiableFields.StudentModifiableField) => {
  editField.value = field;
  showEditModal.value = true;
};

const closeEditModal = () => (showEditModal.value = false);
</script>

<template>
  <div>
    <SectionHeader description="Personal and program details">Basic Information</SectionHeader>

    <div class="mt-2 border-t border-gray-200 dark:border-white/10">
      <DataList>
        <DataItem title="Full Name">{{ student.basic.name }}</DataItem>

        <DataItem title="Registration Number">
          <div class="flex justify-between">
            {{ student.basic.registrationNumber }}
            <SecondaryButtonSmall @click="openEditModal('registration_number')">Edit</SecondaryButtonSmall>
          </div>
        </DataItem>

        <DataItem title="Department and Program">{{ student.basic.department }}</DataItem>

        <DataItem title="Gender">{{ student.basic.gender }}</DataItem>

        <DataItem title="Date of Birth">{{ student.basic.birthDate }}</DataItem>

        <DataItem title="State and Local Government">
          {{ `${student.others.state} (${student.others.localGovernment})` }}
        </DataItem>

        <DataItem title="Entry Mode">{{ student.others.entryMode }}</DataItem>

        <DataItem title="Entry Session">{{ student.others.entrySession }}</DataItem>

        <DataItem title="Entry Level">{{ student.others.entryLevel }}</DataItem>
      </DataList>
    </div>
  </div>

  <div class="border-t border-gray-200 pt-8 dark:border-gray-700">
    <SectionHeader description="Other details">Other Information</SectionHeader>

    <div class="mt-2 border-t border-gray-200 dark:border-white/10">
      <DataList>
        <DataItem title="JAMB Registration Number">{{ student.others.jambRegistrationNumber }}</DataItem>

        <DataItem title="Email">{{ student.others.email }}</DataItem>

        <DataItem title="Phone Number">{{ student.others.phoneNumber }}</DataItem>
      </DataList>
    </div>
  </div>

  <Modal
    :show="showEditModal"
    @close="closeEditModal">
    <BaseSection>
      <component
        :is="componentList[editField]"
        v-if="editField"
        :student="student.basic"
        @close="closeEditModal" />
    </BaseSection>
  </Modal>
</template>
