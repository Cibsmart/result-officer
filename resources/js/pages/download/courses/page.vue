<script lang="ts" setup>
import { Head, Link } from '@inertiajs/vue3';
import { BreadcrumbItem } from '@/types';
import { computed } from 'vue';
import ImportEvents from '@/pages/download/components/importEvents.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Download } from 'lucide-vue-next';
import AppPage from '@/components/AppPage.vue';

const props = defineProps<{
    events: Array<App.Data.Imports.ImportEventData>;
    pending: App.Data.Imports.PendingImportEventData;
}>();

const hasPendingEvent = computed(() => props.pending !== null);

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Course Download', href: route('download.courses.page') }];
</script>

<template>
    <Head title="Download Courses Record" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AppPage
            description="Download Course Records form the Portal"
            title="Download Courses">
            <template #actions>
                <Button
                    :disabled="hasPendingEvent"
                    asChild>
                    <Link
                        :href="route('download.courses.store')"
                        method="post">
                        <Download />
                        Download Courses
                    </Link>
                </Button>
            </template>

            <ImportEvents
                :events="events"
                :pending="pending" />
        </AppPage>
    </AppLayout>
</template>
