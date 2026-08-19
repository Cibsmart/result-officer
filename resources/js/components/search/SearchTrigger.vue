<script lang="ts" setup>
import { useCommandPalette } from '@/composables/useCommandPalette';
import { Search } from 'lucide-vue-next';
import { computed } from 'vue';

const { openPalette } = useCommandPalette();

/** macOS shows ⌘K, everything else Ctrl K — the palette listens for both. */
const shortcut = computed(() =>
    typeof navigator !== 'undefined' && /Mac|iPhone|iPad/.test(navigator.platform) ? '⌘K' : 'Ctrl K',
);
</script>

<template>
    <button
        class="text-muted-foreground flex w-full max-w-xs items-center gap-2 rounded-md border px-3 py-1.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-800"
        type="button"
        @click="openPalette()">
        <Search class="size-4 shrink-0" />

        <span class="flex-1 text-left">Search…</span>

        <kbd class="hidden rounded border px-1.5 py-0.5 text-[10px] sm:inline-block">{{ shortcut }}</kbd>
    </button>
</template>
