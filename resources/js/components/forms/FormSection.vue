<script lang="ts" setup>
defineProps<{
    header?: string;
    description?: string;
}>();
import SectionHeader from '@/components/sectionHeader.vue';
</script>

<template>
    <section>
        <SectionHeader :description="description">
            {{ header }}
        </SectionHeader>

        <slot />
    </section>
</template>
