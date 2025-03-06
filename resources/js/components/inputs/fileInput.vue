<script lang="ts" setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const fileInput = ref<HTMLInputElement | null>(null);

const form = useForm({ file: null as File | null });

const value = false;

const browse = () => fileInput.value?.click();

const onFileChange = () => {
    if (fileInput.value?.files?.length) {
        form.file = fileInput.value.files[0];
    }
};
</script>

<template>
    <div>
        <div
            class="relative block w-full appearance-none rounded border bg-white p-0 text-left leading-normal text-gray-800 focus:border-indigo-500 focus:shadow focus:outline-none">
            <input
                ref="fileInput"
                class="hidden"
                type="file"
                @change="onFileChange" />

            <div
                v-if="!value"
                class="p-2">
                <button
                    class="rounded-sm bg-gray-600 px-4 py-1 text-xs font-medium text-white hover:bg-gray-700 focus:outline-none"
                    type="button"
                    @click="browse">
                    Browse
                </button>
            </div>

            <div
                v-else
                class="flex items-center justify-between p-2">
                <div class="flex-1 pr-1">filename<span class="text-xs text-gray-600">filesize</span></div>

                <button
                    class="rounded-sm bg-gray-600 px-4 py-1 text-xs font-medium text-white hover:bg-gray-700 focus:outline-none"
                    type="button">
                    Remove
                </button>
            </div>
        </div>
    </div>
</template>
