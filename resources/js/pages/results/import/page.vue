<script lang="ts" setup>
import { Head, useForm, usePoll } from '@inertiajs/vue3';
import { PrimaryButton } from '@/components/buttons';
import { InputLabel } from '@/components/inputs';
import { FormGroup, FormSection } from '@/components/forms';
import { InputError } from '@/components/inputs';
import EmptyState from '@/components/emptyState.vue';
import { computed, onMounted, ref, watch } from 'vue';
import { BreadcrumbItem } from '@/types';
import UploadedExcelList from '@/components/UploadedExcelList.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import AppPage from '@/components/AppPage.vue';
import { Card } from '@/components/ui/card';

const props = defineProps<{
    data: App.Data.Imports.ExcelImportEventListData;
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Result Import', href: route('import.results.index') }];

const { start, stop } = usePoll(5000, {}, { autoStart: false });

const form = useForm({ file: null as File | null });

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

const submit = () => {
    form.post(route('import.results.store'));
};

const fileInput = ref<HTMLInputElement | null>(null);
</script>

<template>
    <Head title="Upload Results" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AppPage
            description="Import Results from Excel into the Database"
            title="Result Import">
            <Card class="p-6">
                <FormSection
                    description="Select the Excel Result (.xlsx) File and click Upload"
                    header="Upload Excel Results">
                    <form
                        class="mt-6 space-y-6"
                        @submit.prevent="submit">
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
                        description="You haven't uploaded any Excel results yet."
                        title="No Excel Result Upload Found" />
                </template>
            </Card>
        </AppPage>
    </AppLayout>
</template>
