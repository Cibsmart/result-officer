<script lang="ts" setup>
import { Button } from '@/components/ui/button';

const props = defineProps<{
    href: string;
}>();
</script>

<template>
    <Button
        asChild
        size="sm">
        <a
            :href="props.href"
            class="text-xs font-semibold tracking-widest uppercase"
            target="_blank">
            <slot />
        </a>
    </Button>
</template>
