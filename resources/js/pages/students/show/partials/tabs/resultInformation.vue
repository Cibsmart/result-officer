<script lang="ts" setup>
import EmptyState from '@/components/emptyState.vue';
import IconLink from '@/components/links/iconLink.vue';
import Session from '@/pages/results/index/partials/resultSessionView.vue';
import { computed, ref } from 'vue';
import Modal from '@/components/modal.vue';
import ResultUpdateForm from '@/pages/students/show/partials/updates/resultUpdateForm.vue';
import ResultDeleteForm from '@/pages/students/show/partials/deletes/resultDeleteForm.vue';
import { Card } from '@/components/ui/card';

const props = defineProps<{
    student: App.Data.Students.StudentData;
    results: App.Data.Results.StudentResultData;
}>();

const hasResults = computed(() => props.results.sessionEnrollments.length > 0);

const selectedResult = ref<App.Data.Results.ResultData | null>(null);

const showEditModal = ref(false);
const showDeleteModal = ref(false);

const handleOpenEditResultModal = (result: App.Data.Results.ResultData) => {
    selectedResult.value = result;
    showEditModal.value = true;
};

const handleOpenDeleteResultModal = (result: App.Data.Results.ResultData) => {
    selectedResult.value = result;
    showDeleteModal.value = true;
};

const closeEditModal = () => (showEditModal.value = false);
const closeDeleteModal = () => (showDeleteModal.value = false);
</script>

<template>
    <div>
        <template v-if="hasResults">
            <Session
                v-for="session in results.sessionEnrollments"
                :key="session.id"
                :session="session"
                manageable
                @openDeleteResult="handleOpenDeleteResultModal"
                @openEditResult="handleOpenEditResultModal" />
        </template>

        <EmptyState
            v-else
            description="Get started by downloading student's results from the Portal"
            title="No Result">
            <IconLink :href="route('download.results.page')">Download Results</IconLink>
        </EmptyState>

        <div
            v-if="hasResults"
            class="mt-2 flex flex-col p-2 text-center text-xl font-bold text-black uppercase lg:block dark:text-white">
            <span>
                Current Final CGPA:
                <span>{{ results.formattedFCGPA }} </span>
            </span>

            <span class="hidden lg:inline"> (</span>

            <span class=""> {{ results.degreeClass }}</span>

            <span class="hidden lg:inline">)</span>
        </div>
    </div>

    <Modal
        :show="showEditModal"
        @close="closeEditModal">
        <Card>
            <ResultUpdateForm
                v-if="selectedResult"
                :result="selectedResult"
                :student="student"
                @close="closeEditModal" />
        </Card>
    </Modal>

    <Modal
        :show="showDeleteModal"
        @close="closeDeleteModal">
        <Card>
            <ResultDeleteForm
                v-if="selectedResult"
                :result="selectedResult"
                :student="student"
                @close="closeDeleteModal" />
        </Card>
    </Modal>
</template>
