<script lang="ts" setup>
import { useCommandPalette } from '@/composables/useCommandPalette';
import { MINIMUM_QUERY_LENGTH, useGlobalSearch } from '@/composables/useGlobalSearch';
import { router } from '@inertiajs/vue3';
import { useEventListener } from '@vueuse/core';
import { CornerDownLeft, Loader2, Search } from 'lucide-vue-next';
import {
    DialogContent,
    DialogDescription,
    DialogOverlay,
    DialogPortal,
    DialogRoot,
    DialogTitle,
    VisuallyHidden,
} from 'reka-ui';
import { computed, nextTick, ref, watch } from 'vue';

type SearchResultData = App.Data.Search.SearchResultData;

const { open, closePalette, togglePalette } = useCommandPalette();

const query = ref('');
const highlighted = ref(0);
const listElement = ref<HTMLElement | null>(null);

const { groups, searching, reset } = useGlobalSearch(query);

/** The groups flattened in render order, so arrow keys can walk them as one list. */
const flattened = computed<SearchResultData[]>(() => groups.value.flatMap((group) => group.results));

const showEmptyState = computed(
    () => query.value.trim().length >= MINIMUM_QUERY_LENGTH && !searching.value && flattened.value.length === 0,
);

const indexOf = (result: SearchResultData) => flattened.value.indexOf(result);

const scrollHighlightedIntoView = () => {
    void nextTick(() => {
        listElement.value?.querySelector('[data-highlighted="true"]')?.scrollIntoView({ block: 'nearest' });
    });
};

const move = (offset: number) => {
    const count = flattened.value.length;

    if (count === 0) {
        return;
    }

    highlighted.value = (highlighted.value + offset + count) % count;

    scrollHighlightedIntoView();
};

const visit = (result: SearchResultData | string) => {
    const url = typeof result === 'string' ? result : result.url;

    closePalette();

    /* The Filament panel is not an Inertia app, so those results need a real page load. */
    if (typeof result !== 'string' && result.external) {
        window.location.href = url;

        return;
    }

    router.visit(url);
};

const submit = () => {
    const result = flattened.value[highlighted.value];

    if (result) {
        visit(result);

        return;
    }

    if (query.value.trim().length >= MINIMUM_QUERY_LENGTH) {
        visit(route('search', { q: query.value.trim() }));
    }
};

watch(flattened, () => (highlighted.value = 0));

useEventListener(window, 'keydown', (event: KeyboardEvent) => {
    if (event.key === 'k' && (event.metaKey || event.ctrlKey)) {
        event.preventDefault();
        togglePalette();
    }
});

watch(open, (isOpen) => {
    if (isOpen) {
        return;
    }

    reset();
    query.value = '';
    highlighted.value = 0;
    groups.value = [];
});
</script>

<template>
    <DialogRoot v-model:open="open">
        <DialogPortal>
            <DialogOverlay
                class="data-[state=open]:animate-in data-[state=open]:fade-in-0 fixed inset-0 z-50 bg-black/60" />

            <DialogContent
                class="bg-background data-[state=open]:animate-in data-[state=open]:fade-in-0 data-[state=open]:zoom-in-95 fixed top-[15%] left-[50%] z-50 w-full max-w-[calc(100%-2rem)] translate-x-[-50%] overflow-hidden rounded-lg border shadow-lg sm:max-w-xl">
                <VisuallyHidden>
                    <DialogTitle>Search</DialogTitle>

                    <DialogDescription>Search students, courses and departments.</DialogDescription>
                </VisuallyHidden>

                <div class="flex items-center gap-2 border-b px-4">
                    <Search class="size-4 shrink-0 text-gray-500 dark:text-gray-400" />

                    <input
                        v-model="query"
                        aria-label="Search"
                        autocomplete="off"
                        class="placeholder:text-muted-foreground h-12 w-full bg-transparent text-sm outline-hidden"
                        placeholder="Search students, courses, departments…"
                        type="text"
                        @keydown.down.prevent="move(1)"
                        @keydown.enter.prevent="submit"
                        @keydown.up.prevent="move(-1)" />

                    <Loader2
                        v-if="searching"
                        class="size-4 shrink-0 animate-spin text-gray-500 dark:text-gray-400" />
                </div>

                <div
                    ref="listElement"
                    class="max-h-[22rem] overflow-y-auto overscroll-contain p-2">
                    <p
                        v-if="query.trim().length < MINIMUM_QUERY_LENGTH"
                        class="text-muted-foreground px-2 py-6 text-center text-sm">
                        Type at least {{ MINIMUM_QUERY_LENGTH }} characters to search.
                    </p>

                    <p
                        v-else-if="showEmptyState"
                        class="text-muted-foreground px-2 py-6 text-center text-sm">
                        No results for “{{ query.trim() }}”.
                    </p>

                    <div
                        v-for="group in groups"
                        :key="group.key"
                        class="mb-1">
                        <p class="text-muted-foreground px-2 py-1.5 text-xs font-medium tracking-wide uppercase">
                            {{ group.label }}
                        </p>

                        <button
                            v-for="result in group.results"
                            :key="`${group.key}-${result.id}`"
                            :data-highlighted="indexOf(result) === highlighted"
                            class="flex w-full items-center gap-2 rounded-md px-2 py-2 text-left text-sm data-[highlighted=true]:bg-gray-100 dark:data-[highlighted=true]:bg-gray-800"
                            type="button"
                            @click="visit(result)"
                            @mousemove="highlighted = indexOf(result)">
                            <span class="min-w-0 flex-1">
                                <span class="block truncate font-medium">{{ result.title }}</span>

                                <span
                                    v-if="result.subtitle"
                                    class="text-muted-foreground block truncate text-xs">
                                    {{ result.subtitle }}
                                </span>
                            </span>

                            <span
                                v-if="result.badge"
                                class="shrink-0 rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-gray-400/10 dark:text-gray-400">
                                {{ result.badge }}
                            </span>
                        </button>
                    </div>
                </div>

                <div class="text-muted-foreground flex items-center justify-between border-t px-4 py-2 text-xs">
                    <span class="flex items-center gap-1">
                        <CornerDownLeft class="size-3" />

                        to open
                    </span>

                    <button
                        v-if="query.trim().length >= MINIMUM_QUERY_LENGTH"
                        class="hover:text-foreground underline underline-offset-2"
                        type="button"
                        @click="visit(route('search', { q: query.trim() }))">
                        See all results
                    </button>
                </div>
            </DialogContent>
        </DialogPortal>
    </DialogRoot>
</template>
