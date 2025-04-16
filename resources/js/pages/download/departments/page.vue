<script lang="ts" setup>
import { Head, useForm } from '@inertiajs/vue3';
import BasePage from '@/layouts/main/partials/basePage.vue';
import BaseHeader from '@/layouts/main/partials/baseHeader.vue';
import BaseSection from '@/layouts/main/partials/baseSection.vue';
import { BreadcrumbsItem } from '@/types';
import Breadcrumb from '@/components/breadcrumb.vue';
import PrimaryButton from '@/components/buttons/primaryButton.vue';
import BaseFormSection from '@/components/forms/baseFormSection.vue';
import { computed } from 'vue';
import ImportEvents from '@/pages/download/components/importEvents.vue';

const props = defineProps<{
    events: Array<App.Data.Imports.ImportEventData>;
    pending: App.Data.Imports.PendingImportEventData;
}>();

const hasPendingEvent = computed(() => props.pending !== null);

const disableButton = computed(() => form.processing || hasPendingEvent.value);

const pages: BreadcrumbsItem[] = [
    {
        name: 'Download Departments',
        href: route('download.departments.page'),
        current: route().current('download.departments.page'),
    },
];

const form = useForm({});

const submit = () => {
    form.post(route('download.departments.store'));
};
</script>

<template>
    <Head title="Download Department Records" />

    <Breadcrumb :pages="pages" />

    <BaseHeader>Download Department Records</BaseHeader>

    <BasePage>
        <BaseSection>
            <BaseFormSection
                description="Click to Download All Departments from the Portal"
                header="Download Departments">
                <form
                    class="mt-6 space-y-6"
                    @submit.prevent="submit">
                    <div>
                        <PrimaryButton :disabled="disableButton">Download</PrimaryButton>
                    </div>
                </form>
            </BaseFormSection>
        </BaseSection>

        <ImportEvents
            :events="events"
            :pending="pending" />
    </BasePage>
</template>
