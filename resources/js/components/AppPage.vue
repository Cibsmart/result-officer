<script lang="ts" setup>
import Heading from '@/components/Heading.vue';

defineProps<{
    title: string;
    description: string;
}>();
</script>
<template>
    <div class="space-y-4 p-6">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <Heading
                    :description="description"
                    :title="title" />
            </div>

            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <slot name="actions" />
            </div>
        </div>

        <slot />
    </div>
</template>
