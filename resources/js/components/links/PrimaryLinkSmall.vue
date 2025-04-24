<script lang="ts" setup>
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    href: string;
}>();
</script>

<template>
    <Button
        asChild
        size="sm">
        <Link
            :href="props.href"
            class="text-xs tracking-widest uppercase">
            <slot />
        </Link>
    </Button>
</template>
