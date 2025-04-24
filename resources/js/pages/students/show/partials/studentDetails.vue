<script lang="ts" setup>
import BaseTabPanel from '@/components/tabs/baseTabPanel.vue';
import BaseTabs from '@/components/tabs/baseTabs.vue';
import BasicInformation from '@/pages/students/show/partials/tabs/basicInformation.vue';
import StudentPageHeader from '@/pages/students/show/partials/studentPageHeader.vue';
import ResultInformation from '@/pages/students/show/partials/tabs/resultInformation.vue';
import { ref } from 'vue';
import Modal from '@/components/modal.vue';
import StudentDeleteForm from '@/pages/students/show/partials/deletes/studentDeleteForm.vue';
import { Card } from '@/components/ui/card';

defineProps<{
    student: App.Data.Students.StudentData;
    results: App.Data.Results.StudentResultData;
    selectedIndex: number;
}>();

const tabs = [
    { name: 'Details', href: '#', current: true },
    { name: 'Results', href: '#', current: false },
    { name: 'History', href: '#', current: false },
];

const selectedStudent = ref<App.Data.Students.StudentBasicData | null>(null);

const handleOpenDeleteStudentModal = (student: App.Data.Students.StudentBasicData) => {
    selectedStudent.value = student;
    openDeleteStudentForm.value = true;
};

const openStatusUpdateForm = ref(false);
const openDeleteStudentForm = ref(false);

const closeDeleteModal = () => (openDeleteStudentForm.value = false);
</script>

<template>
    <div class="relative pb-5 sm:pb-0">
        <StudentPageHeader
            :student="student.basic"
            @openDeleteStudent="handleOpenDeleteStudentModal"
            @openUpdateStatus="openStatusUpdateForm = true" />

        <div class="mt-3 sm:mt-4">
            <BaseTabs
                :selectedIndex="selectedIndex"
                :tabs="tabs">
                <BaseTabPanel>
                    <BasicInformation
                        :openStatusUpdateForm="openStatusUpdateForm"
                        :student="student"
                        @closeStatusUpdateForm="openStatusUpdateForm = false" />
                </BaseTabPanel>

                <BaseTabPanel>
                    <ResultInformation
                        :results="results"
                        :student="student" />
                </BaseTabPanel>

                <BaseTabPanel>Student History</BaseTabPanel>
            </BaseTabs>
        </div>
    </div>

    <Modal
        :show="openDeleteStudentForm"
        @close="closeDeleteModal">
        <Card class="p-6">
            <StudentDeleteForm
                v-if="selectedStudent"
                :student="student.basic"
                @close="closeDeleteModal" />
        </Card>
    </Modal>
</template>
