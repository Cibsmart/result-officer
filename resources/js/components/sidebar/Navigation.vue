<script lang="ts" setup>
import { NavItem } from '@/types';
import { LayoutGrid, Download, Users, Folders, BookCheck, ListCheck, GraduationCap } from 'lucide-vue-next';
import NavMain from '@/components/NavMain.vue';

const mainNav: NavItem[] = [
    { title: 'Dashboard', href: route('dashboard'), icon: LayoutGrid, isActive: route().current('dashboard') },
    {
        title: 'Download',
        href: '#',
        icon: Download,
        isActive: true,
        items: [
            { title: 'Courses', href: route('download.courses.page') },
            { title: 'Departments', href: route('download.departments.page') },
            { title: 'Students', href: route('download.students.page', { selectedIndex: 0 }) },
            { title: 'Registrations', href: route('download.registrations.page', { selectedIndex: 0 }) },
            { title: 'Results', href: route('download.results.page', { selectedIndex: 0 }) },
        ],
    },
    {
        title: 'Students',
        href: '#',
        icon: Users,
        isActive: false,
        items: [
            { title: 'List', href: route('students.index') },
            { title: 'View', href: route('students.show') },
        ],
    },
    {
        title: 'Results',
        href: '#',
        icon: Folders,
        isActive: false,
        items: [
            { title: 'View', href: route('results.index') },
            { title: 'Summary', href: route('summary.form') },
            { title: 'Composite', href: route('composite.form') },
            { title: 'Upload Excel', href: route('import.results.index') },
            { title: 'Export Excel', href: route('export.results.page') },
        ],
    },
    {
        title: 'Reconciliation',
        href: '#',
        icon: BookCheck,
        isActive: false,
        items: [
            { title: 'Possible Graduands', href: route('graduand.index') },
            { title: 'Vet Students', href: route('vettingEvent.index') },
            { title: 'Cleared Students', href: route('department.cleared.index') },
        ],
    },
    {
        title: 'Final Results',
        href: '#',
        icon: GraduationCap,
        isActive: false,
        items: [
            { title: 'View', href: route('finalResults.index') },
            { title: 'Upload', href: route('import.final-results.index') },
        ],
    },
    {
        title: 'Course List',
        href: '#',
        icon: ListCheck,
        isActive: false,
        items: [{ title: 'Upload', href: route('import.curriculum.index') }],
    },
];
</script>

<template>
    <NavMain
        :items="mainNav"
        label="Main" />
</template>
