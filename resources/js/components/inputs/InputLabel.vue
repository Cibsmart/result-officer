<script lang="ts" setup>
import { Label } from '@/components/ui/label';

defineProps<{
    value?: string;
}>();
</script>

<template>
    <Label>
        <slot />
    </Label>
</template>
