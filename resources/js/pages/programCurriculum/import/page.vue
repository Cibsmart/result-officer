<script lang="ts" setup>
import { Head, useForm } from '@inertiajs/vue3';
import Breadcrumb from '@/components/breadcrumb.vue';
import BaseHeader from '@/layouts/main/partials/baseHeader.vue';
import BasePage from '@/layouts/main/partials/basePage.vue';
import BaseSection from '@/layouts/main/partials/baseSection.vue';
import PrimaryButton from '@/components/buttons/primaryButton.vue';
import InputLabel from '@/components/inputs/inputLabel.vue';
import FormGroup from '@/components/forms/formGroup.vue';
import BaseFormSection from '@/components/forms/baseFormSection.vue';
import InputError from '@/components/inputs/inputError.vue';
import EmptyState from '@/components/emptyState.vue';
import { computed, ref, watch, onMounted } from 'vue';
import { BreadcrumbItem } from '@/types';
import { usePoll } from '@inertiajs/vue3';
import SelectInput from '@/components/inputs/selectInput.vue';
import UploadedExcelList from '@/components/uploadedExcelList.vue';

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

const pages: BreadcrumbItem[] = [
    {
        name: 'Final Result Import',
        href: route('import.curriculum.index'),
        current: route().current('import.curriculum.index'),
    },
];

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

    <Breadcrumb :pages="pages" />

    <BaseHeader>Import Curriculum (Course List)</BaseHeader>

    <BasePage>
        <BaseSection>
            <BaseFormSection
                description="Select the Course List Excel (.xlsx) File and click Upload"
                header="Upload Course List">
                <form
                    class="mt-6 space-y-6"
                    @submit.prevent="submit">
                    <FormGroup>
                        <div class="flex-1 shrink-0">
                            <InputLabel
                                for="department"
                                value="Department" />

                            <SelectInput
                                id="department"
                                v-model="form.department"
                                :items="departments.data"
                                @update:modelValue="loadPrograms" />

                            <InputError :message="form.errors.department" />
                        </div>

                        <div class="flex-1 shrink-0">
                            <InputLabel
                                for="program"
                                value="Program" />

                            <SelectInput
                                id="program"
                                v-model="form.program"
                                :items="programs" />

                            <InputError :message="form.errors.program" />
                        </div>
                    </FormGroup>

                    <FormGroup>
                        <div class="flex-1">
                            <InputLabel
                                for="file"
                                value="Excel File" />

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
            </BaseFormSection>
        </BaseSection>

        <BaseSection>
            <template v-if="hasEvent">
                <UploadedExcelList :data="data" />
            </template>

            <template v-else>
                <EmptyState
                    description="Start by selecting a file to upload and click Upload"
                    title="No Course List Upload Found" />
            </template>
        </BaseSection>
    </BasePage>
</template>
