<script lang="ts" setup>
import { Head, Link } from '@inertiajs/vue3';
import { BreadcrumbItem } from '@/types';
import { computed } from 'vue';
import ImportEvents from '@/pages/download/components/importEvents.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import AppPage from '@/components/AppPage.vue';
import { Button } from '@/components/ui/button';
import { Download } from 'lucide-vue-next';

const props = defineProps<{
    events: Array<App.Data.Imports.ImportEventData>;
    pending: App.Data.Imports.PendingImportEventData;
}>();

const hasPendingEvent = computed(() => props.pending !== null);

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Department Download', href: route('download.departments.page') }];
</script>

<template>
    <Head title="Download Department Records" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AppPage
            description="Click to Download All Departments from the Portal"
            title="Download Departments">
            <template #actions>
                <Button
                    :disabled="hasPendingEvent"
                    asChild>
                    <Link
                        :href="route('download.departments.store')"
                        method="post">
                        <Download />
                        Download Departments
                    </Link>
                </Button>
            </template>

            <ImportEvents
                :events="events"
                :pending="pending" />
        </AppPage>
    </AppLayout>
</template>
