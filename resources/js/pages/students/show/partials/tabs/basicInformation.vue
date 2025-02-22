<script lang="ts" setup>
import DataList from "@/components/data/dataList.vue";
import { ref, defineAsyncComponent, watch } from "vue";
import DataItem from "@/components/data/dataItem.vue";
import SectionHeader from "@/components/sectionHeader.vue";
import SecondaryButtonSmall from "@/components/buttons/secondaryButtonSmall.vue";
import Modal from "@/components/modal.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";

const props = defineProps<{
  student: App.Data.Students.StudentData;
  statues: App.Data.Enums.StudentStatusListData;
  openStatusUpdateForm: boolean;
}>();

const emit = defineEmits<(e: "closeStatusUpdateForm") => void>();

const editField = ref<App.Enums.ModifiableFields.StudentModifiableField | "">("");
const showEditModal = ref(false);

const componentList: Record<
  App.Enums.ModifiableFields.StudentModifiableField,
  ReturnType<typeof defineAsyncComponent>
> = {
  registration_number: defineAsyncComponent(
    () => import("@/pages/students/show/partials/updates/registrationNumberForm.vue"),
  ),
  name: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/nameUpdateForm.vue")),
  status: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/statusUpdateForm.vue")),
  result: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/resultUpdateForm.vue")),
  // course: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/courseUpdateForm.vue")),
  gender: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/genderForm.vue")),
  program: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/programForm.vue")),
  entry_mode: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/entryModeForm.vue")),
  entry_level: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/entryLevelForm.vue")),
  entry_session: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/entrySessionForm.vue")),
  date_of_birth: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/birthDateForm.vue")),
  local_government: defineAsyncComponent(
    () => import("@/pages/students/show/partials/updates/localGovernmentForm.vue"),
  ),
  email: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/emailForm.vue")),
  phone_number: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/phoneNumberForm.vue")),
  jamb_registration_number: defineAsyncComponent(() => import("@/pages/students/show/partials/updates/jambForm.vue")),
};

watch(
  () => props.openStatusUpdateForm,
  () => {
    if (props.openStatusUpdateForm) openEditModal("status");
  },
);

const openEditModal = (field: App.Enums.ModifiableFields.StudentModifiableField) => {
  editField.value = field;
  showEditModal.value = true;
};

const closeEditModal = () => {
  showEditModal.value = false;
  emit("closeStatusUpdateForm");
};
</script>

<template>
  <div>
    <SectionHeader description="Personal and program details">Basic Information</SectionHeader>

    <div class="mt-2 border-t border-gray-200 dark:border-white/10">
      <DataList>
        <DataItem title="Full Name">
          <div class="flex justify-between">
            {{ student.basic.name }}
            <SecondaryButtonSmall @click="openEditModal('name')">Edit</SecondaryButtonSmall>
          </div>
        </DataItem>

        <DataItem title="Registration Number">
          <div class="flex justify-between">
            {{ student.basic.registrationNumber }}
            <SecondaryButtonSmall @click="openEditModal('registration_number')">Edit</SecondaryButtonSmall>
          </div>
        </DataItem>

        <DataItem title="Department and Program">
          <div class="flex justify-between">
            {{ student.basic.departmentProgram }}
            <SecondaryButtonSmall @click="openEditModal('program')">Edit</SecondaryButtonSmall>
          </div>
        </DataItem>

        <DataItem title="Gender">
          <div class="flex justify-between">
            {{ student.basic.gender }}
            <SecondaryButtonSmall @click="openEditModal('gender')">Edit</SecondaryButtonSmall>
          </div>
        </DataItem>

        <DataItem title="Date of Birth">
          <div class="flex justify-between">
            <span>{{ student.basic.birthDate }}</span>

            <SecondaryButtonSmall
              class="end-0"
              @click="openEditModal('date_of_birth')"
              >Edit
            </SecondaryButtonSmall>
          </div>
        </DataItem>

        <DataItem title="State and Local Government">
          <div class="flex justify-between">
            {{ `${student.others.state} (${student.others.localGovernment})` }}
            <SecondaryButtonSmall @click="openEditModal('local_government')">Edit</SecondaryButtonSmall>
          </div>
        </DataItem>

        <DataItem title="Entry Mode">
          <div class="flex justify-between">
            {{ student.others.entryMode }}
            <SecondaryButtonSmall @click="openEditModal('entry_mode')">Edit</SecondaryButtonSmall>
          </div>
        </DataItem>

        <DataItem title="Entry Session">
          <div class="flex justify-between">
            {{ student.others.entrySession }}
            <SecondaryButtonSmall @click="openEditModal('entry_session')">Edit</SecondaryButtonSmall>
          </div>
        </DataItem>

        <DataItem title="Entry Level">
          <div class="flex justify-between">
            {{ student.others.entryLevel }}
            <SecondaryButtonSmall @click="openEditModal('entry_level')">Edit</SecondaryButtonSmall>
          </div>
        </DataItem>
      </DataList>
    </div>
  </div>

  <div class="border-t border-gray-200 pt-8 dark:border-gray-700">
    <SectionHeader description="Other details">Other Information</SectionHeader>

    <div class="mt-2 border-t border-gray-200 dark:border-white/10">
      <DataList>
        <DataItem title="JAMB Registration Number">
          <div class="flex justify-between">
            <span>{{ student.others.jambRegistrationNumber }}</span>

            <SecondaryButtonSmall @click="openEditModal('jamb_registration_number')">Edit</SecondaryButtonSmall>
          </div>
        </DataItem>

        <DataItem title="Email">
          <div class="flex justify-between">
            <span>{{ student.others.email }}</span>

            <SecondaryButtonSmall @click="openEditModal('email')">Edit</SecondaryButtonSmall>
          </div>
        </DataItem>

        <DataItem title="Phone Number">
          <div class="flex justify-between">
            <span>{{ student.others.phoneNumber }}</span>

            <SecondaryButtonSmall @click="openEditModal('phone_number')">Edit</SecondaryButtonSmall>
          </div>
        </DataItem>
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
        :statues="statues.data"
        :student="student"
        @close="closeEditModal" />
    </BaseSection>
  </Modal>
</template>
