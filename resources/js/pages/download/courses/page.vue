<script lang="ts" setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { BreadcrumbItem } from '@/types';
import { computed } from 'vue';
import ImportEvents from '@/pages/download/components/importEvents.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Download } from 'lucide-vue-next';

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
        <div class="space-y-4 px-6 py-6">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <Heading
                        description="Download Course Records from the Portal"
                        title="Download Courses" />
                </div>

                <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
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
                </div>
            </div>

            <ImportEvents
                :events="events"
                :pending="pending" />
        </div>
    </AppLayout>
</template>
