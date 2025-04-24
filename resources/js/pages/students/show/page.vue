<script lang="ts" setup>
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import StudentShowForm from '@/pages/students/show/partials/studentShowForm.vue';
import StudentDetails from '@/pages/students/show/partials/studentDetails.vue';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import AppPage from '@/components/AppPage.vue';
import { Card } from '@/components/ui/card';

const props = defineProps<{
    data: App.Data.Students.StudentComprehensiveData;
    selectedIndex: number;
}>();

const showForm = computed(() => props.data === null);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Students', href: route('students.index') },
    { title: 'Student', href: '#' },
];
</script>

<template>
    <Head title="Student Page" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AppPage
            description="View Student's Information, Results and History"
            title="Student Detail Page">
            <Card
                v-if="showForm"
                class="p-6">
                <StudentShowForm />
            </Card>

            <Card
                v-else
                class="p-6">
                <StudentDetails
                    :results="data.results"
                    :selectedIndex="selectedIndex"
                    :student="data.student" />
            </Card>
        </AppPage>
    </AppLayout>
</template>
