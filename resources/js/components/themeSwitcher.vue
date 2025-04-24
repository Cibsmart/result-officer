<script lang="ts" setup>
import { onMounted, ref, watch } from 'vue';
import { RadioGroup, RadioGroupOption } from '@headlessui/vue';

const theme = ref(localStorage.getItem('theme') || 'system');

const applyTheme = () => {
    const html = document.documentElement;
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    html.classList.remove('light', 'dark');

    if (theme.value === 'dark' || (theme.value === 'system' && systemPrefersDark)) {
        html.classList.add('dark');
    }

    localStorage.setItem('theme', theme.value);
};

watch(theme, () => applyTheme());

onMounted(() => applyTheme());
</script>

<template>
    <RadioGroup
        v-model="theme"
        class="relative z-0 inline-grid grid-cols-3 gap-1 rounded-full bg-gray-950/5 p-0.75 text-gray-950 dark:bg-white/10 dark:text-white">
        <RadioGroupOption
            v-slot="{ checked }"
            value="system">
            <span
                :class="
                    checked
                        ? 'bg-white inset-ring ring ring-gray-950/10 inset-ring-white/10 dark:bg-gray-700 dark:text-white dark:ring-transparent'
                        : ''
                "
                class="rounded-full p-1.5 *:size-7 sm:p-0">
                <svg
                    class="size-6"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25"
                        stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </span>
        </RadioGroupOption>

        <RadioGroupOption
            v-slot="{ checked }"
            value="light">
            <span
                :class="
                    checked
                        ? 'bg-white inset-ring ring ring-gray-950/10 inset-ring-white/10 dark:bg-gray-700 dark:text-white dark:ring-transparent'
                        : ''
                ">
                <svg
                    class="size-6"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
                        stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </span>
        </RadioGroupOption>

        <RadioGroupOption
            v-slot="{ checked }"
            value="dark">
            <span
                :class="
                    checked
                        ? 'bg-white inset-ring ring ring-gray-950/10 inset-ring-white/10 dark:bg-gray-700 dark:text-white dark:ring-transparent'
                        : ''
                ">
                <svg
                    class="size-6"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"
                        stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </span>
        </RadioGroupOption>
    </RadioGroup>
</template>
