<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import BaseHeader from '@/layouts/main/partials/baseHeader.vue';
import BasePage from '@/layouts/main/partials/basePage.vue';
import Breadcrumb from '@/components/breadcrumb.vue';
import BaseSection from '@/layouts/main/partials/baseSection.vue';
import ThemeSwitcher from '@/components/themeSwitcher.vue';
</script>

<template>
    <Head title="Dashboard" />

    <Breadcrumb />

    <BaseHeader>Dashboard</BaseHeader>

    <BasePage>
        <BaseSection>
            <div class="py-12">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow-xs sm:rounded-lg">
                        <div class="p-6">You're logged in!</div>
                    </div>
                </div>
            </div>
        </BaseSection>

        <ThemeSwitcher />
    </BasePage>
</template>
