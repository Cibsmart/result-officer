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
            {
                title: 'Courses',
                href: route('download.courses.page'),
                isActive: route().current('download.courses.page'),
            },
            {
                title: 'Departments',
                href: route('download.departments.page'),
                isActive: route().current('download.departments.page'),
            },
            {
                title: 'Students',
                href: route('download.students.page', { selectedIndex: 0 }),
                isActive: route().current('download.students.page'),
            },
            {
                title: 'Registrations',
                href: route('download.registrations.page', { selectedIndex: 0 }),
                isActive: route().current('download.registrations.page'),
            },
            {
                title: 'Results',
                href: route('download.results.page', { selectedIndex: 0 }),
                isActive: route().current('download.results.page'),
            },
        ],
    },
    {
        title: 'Students',
        href: '#',
        icon: Users,
        isActive: false,
        items: [
            { title: 'List', href: route('students.index'), isActive: route().current('students.index') },
            { title: 'View', href: route('students.show'), isActive: route().current('students.show') },
        ],
    },
    {
        title: 'Results',
        href: '#',
        icon: Folders,
        isActive: false,
        items: [
            { title: 'View', href: route('results.index'), isActive: route().current('results.index') },
            { title: 'Summary', href: route('summary.form'), isActive: route().current('summary.form') },
            { title: 'Composite', href: route('composite.form'), isActive: route().current('composite.form') },
            {
                title: 'Upload Excel',
                href: route('import.results.index'),
                isActive: route().current('import.results.index'),
            },
            {
                title: 'Export Excel',
                href: route('export.results.page'),
                isActive: route().current('export.results.page'),
            },
        ],
    },
    {
        title: 'Reconciliation',
        href: '#',
        icon: BookCheck,
        isActive: false,
        items: [
            { title: 'Possible Graduands', href: route('graduand.index'), isActive: route().current('graduand.index') },
            {
                title: 'Vet Students',
                href: route('vettingEvent.index'),
                isActive: route().current('vettingEvent.index'),
            },
            {
                title: 'Cleared Students',
                href: route('department.cleared.index'),
                isActive: route().current('department.cleared.index'),
            },
        ],
    },
    {
        title: 'Final Results',
        href: '#',
        icon: GraduationCap,
        isActive: false,
        items: [
            { title: 'View', href: route('finalResults.index'), isActive: route().current('finalResults.index') },
            {
                title: 'Upload',
                href: route('import.final-results.index'),
                isActive: route().current('import.final-results.index'),
            },
        ],
    },
    {
        title: 'Course List',
        href: '#',
        icon: ListCheck,
        isActive: false,
        items: [
            {
                title: 'Upload',
                href: route('import.curriculum.index'),
                isActive: route().current('import.curriculum.index'),
            },
        ],
    },
];
</script>

<template>
    <NavMain
        :items="mainNav"
        label="Main" />
</template>
