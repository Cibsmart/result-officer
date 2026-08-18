import { ref } from 'vue';

const open = ref(false);

/**
 * The command palette lives once in the app layout, so its open state is
 * module scoped — any trigger (header button, keyboard shortcut) flips the
 * same ref rather than owning a dialog of its own.
 */
export function useCommandPalette() {
    return {
        open,
        openPalette: () => (open.value = true),
        closePalette: () => (open.value = false),
        togglePalette: () => (open.value = !open.value),
    };
}
