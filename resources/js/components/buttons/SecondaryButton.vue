<script lang="ts" setup>
import { Button } from '@/components/ui/button';

withDefaults(
    defineProps<{
        type?: 'button' | 'submit' | 'reset';
    }>(),
    {
        type: 'button',
    },
);
</script>

<template>
    <Button
        :type="type"
        class="text-xs font-semibold tracking-widest uppercase"
        variant="secondary">
        <slot />
    </Button>
</template>
