<script lang="ts" setup>
import { Switch, SwitchGroup, SwitchLabel } from '@headlessui/vue';

withDefaults(defineProps<{ label?: string }>(), { label: '' });

const enabled = defineModel<boolean>('modelValue');
</script>

<template>
    <SwitchGroup
        as="div"
        class="flex items-center">
        <Switch
            v-model="enabled"
            :class="[enabled ? 'bg-indigo-600' : 'bg-gray-300 dark:bg-black']"
            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:outline-hidden">
            <span class="sr-only">Use setting</span>

            <span
                :class="[enabled ? 'translate-x-5' : 'translate-x-0']"
                class="pointer-events-none relative inline-block size-5 transform rounded-full bg-white ring-0 shadow-sm transition duration-200 ease-in-out">
                <span
                    :class="[enabled ? 'opacity-0 duration-100 ease-out' : 'opacity-100 duration-200 ease-in']"
                    aria-hidden="true"
                    class="absolute inset-0 flex size-full items-center justify-center transition-opacity">
                    <svg
                        class="size-3 text-gray-400"
                        fill="none"
                        viewBox="0 0 12 12">
                        <path
                            d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2"
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2" />
                    </svg>
                </span>

                <span
                    :class="[enabled ? 'opacity-100 duration-200 ease-in' : 'opacity-0 duration-100 ease-out']"
                    aria-hidden="true"
                    class="absolute inset-0 flex size-full items-center justify-center transition-opacity">
                    <svg
                        class="size-3 text-indigo-600"
                        fill="currentColor"
                        viewBox="0 0 12 12">
                        <path
                            d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z" />
                    </svg>
                </span>
            </span>
        </Switch>

        <SwitchLabel
            as="span"
            class="ml-3 text-sm">
            <span class="font-medium text-gray-900 dark:text-gray-200">{{ label }}</span>
        </SwitchLabel>
    </SwitchGroup>
</template>
