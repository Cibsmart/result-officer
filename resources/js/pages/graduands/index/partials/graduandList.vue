<script lang="ts" setup>
import { computed, ref } from 'vue';
import EmptyState from '@/components/emptyState.vue';
import { IconLink } from '@/components/links';
import StudentRow from '@/pages/graduands/index/partials/graduandRow.vue';
import Modal from '@/components/modal.vue';
import { BaseTable, BaseTBody, BaseTH, BaseTHead, BaseTR } from '@/components/tables';
import { PaginatedGraduandListData } from '@/types/paginate';
import Pagination from '@/components/pagination.vue';
import ClearanceConfirmationForm from '@/pages/graduands/index/partials/clearanceConfirmationForm.vue';
import VettingDetailDrawer from '@/pages/vetting/show/partials/vettingDetailDrawer.vue';
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';

const props = defineProps<{
    department: App.Data.Department.DepartmentInfoData;
    paginated: PaginatedGraduandListData;
}>();

const showReport = ref(false);

const currentStudentSlug = ref('');

const hasRows = computed(() => props.paginated.data.length > 0);

const openDrawer = (student: App.Data.Graduands.GraduandData) => {
    currentStudentSlug.value = student.slug;
    showReport.value = true;
};

const openClearanceForm = ref(false);
const clearanceStudent = ref<App.Data.Graduands.GraduandData>();

const confirmStudentClearance = (student: App.Data.Graduands.GraduandData) => {
    clearanceStudent.value = student;
    openClearanceForm.value = true;
};

const closeModal = () => (openClearanceForm.value = false);
</script>

<template>
    <Card class="py-4">
        <CardHeader>
            <div
                class="mt-1 divide-y divide-solid divide-gray-300 ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:divide-gray-600 dark:ring-gray-600">
                <div class="grid grid-flow-col">
                    <div class="p-2">
                        FACULTY: <span class="font-bold text-black dark:text-white">{{ department.faculty.name }}</span>
                    </div>
                </div>

                <div class="grid grid-flow-col">
                    <div class="p-2">
                        DEPARTMENT:
                        <span class="font-bold text-black dark:text-white">{{ department.department.name }}</span>
                    </div>
                </div>
            </div>
        </CardHeader>

        <CardContent>
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
        </CardContent>

        <CardFooter class="mt-4">
            <Pagination :paginated="paginated" />
        </CardFooter>
    </Card>

    <Modal
        :show="openClearanceForm"
        @close="closeModal">
        <Card v-if="clearanceStudent">
            <ClearanceConfirmationForm
                :student="clearanceStudent"
                @close="closeModal" />
        </Card>
    </Modal>

    <VettingDetailDrawer
        :openReportDrawer="showReport"
        :slug="currentStudentSlug"
        @close="showReport = false" />
</template>
