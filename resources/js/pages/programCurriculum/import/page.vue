<script lang="ts" setup>
import { Head, useForm, usePoll } from '@inertiajs/vue3';
import { PrimaryButton } from '@/components/buttons';
import { InputError, InputLabel, SelectInput } from '@/components/inputs';
import { FormGroup, FormSection } from '@/components/forms';
import EmptyState from '@/components/emptyState.vue';
import { computed, onMounted, ref, watch } from 'vue';
import { BreadcrumbItem } from '@/types';
import UploadedExcelList from '@/components/UploadedExcelList.vue';
import AppPage from '@/components/AppPage.vue';
import { Card } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    data: App.Data.Imports.ExcelImportEventListData;
    departments: App.Data.Department.DepartmentListData;
}>();

const { start, stop } = usePoll(5000, {}, { autoStart: false });

const form = useForm({
    department: null as App.Data.Department.DepartmentData | null,
    file: null as File | null,
    program: '',
});

const submit = () => {
    form.post(route('import.curriculum.store'));
};

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Course List Import', href: route('import.curriculum.index') }];

const hasEvent = computed(() => props.data.events.length > 0);

const hasUnfinishedImport = computed(() =>
    props.data.events.some((importEvent) => importEvent.status !== 'completed' && importEvent.status !== 'failed'),
);

onMounted(() => {
    if (hasUnfinishedImport.value) {
        start();
    }
});

watch(hasUnfinishedImport, () => {
    if (hasUnfinishedImport.value) {
        start();
    } else {
        stop();
    }
});

const onFileChange = () => {
    if (fileInput.value?.files?.length) {
        form.file = fileInput.value.files[0];
    }
};

const loadPrograms = () => {
    form.reset('program');
    if (form.department !== null && form.department.programs !== null) {
        programs.value = form.department.programs.programs;
    }
};

const fileInput = ref<HTMLInputElement | null>(null);
const programs = ref([{ id: 0, name: 'Select Program' }]);
</script>

<template>
    <Head title="Import Curriculum" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AppPage
            description="Import or Upload Course List from Excel to the Database"
            title="Course List Import">
            <Card class="p-6">
                <FormSection
                    description="Select the Course List Excel (.xlsx) File and click Upload"
                    header="Upload Course List">
                    <form
                        class="mt-6 space-y-6"
                        @submit.prevent="submit">
                        <FormGroup>
                            <div class="grid flex-1 shrink-0 gap-2">
                                <InputLabel for="department">Department</InputLabel>

                                <SelectInput
                                    id="department"
                                    v-model="form.department"
                                    :items="departments.data"
                                    @update:modelValue="loadPrograms" />

                                <InputError :message="form.errors.department" />
                            </div>

                            <div class="grid flex-1 shrink-0 gap-2">
                                <InputLabel for="program">Program</InputLabel>

                                <SelectInput
                                    id="program"
                                    v-model="form.program"
                                    :items="programs" />

                                <InputError :message="form.errors.program" />
                            </div>
                        </FormGroup>

                        <FormGroup>
                            <div class="grid flex-1 gap-2">
                                <InputLabel for="file">Excel File</InputLabel>

                                <input
                                    ref="fileInput"
                                    type="file"
                                    @change="onFileChange" />

                                <progress
                                    v-if="form.progress"
                                    :value="form.progress.percentage"
                                    max="100">
                                    {{ form.progress.percentage }}%
                                </progress>

                                <InputError :message="form.errors.file" />
                            </div>

                            <div>
                                <PrimaryButton :disabled="form.processing">Upload</PrimaryButton>
                            </div>
                        </FormGroup>
                    </form>
                </FormSection>
            </Card>

            <Card>
                <template v-if="hasEvent">
                    <UploadedExcelList :data="data" />
                </template>

                <template v-else>
                    <EmptyState
                        description="Start by selecting a file to upload and click Upload"
                        title="No Course List Upload Found" />
                </template>
            </Card>
        </AppPage>
    </AppLayout>
</template>
