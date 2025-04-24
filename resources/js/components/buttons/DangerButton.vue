<script lang="ts" setup>
import { Button } from '@/components/ui/button';
</script>
<template>
    <Button
        class="text-xs font-semibold tracking-widest uppercase"
        type="button"
        variant="destructive">
        <slot />
    </Button>
</template>
