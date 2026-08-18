import { ref, watch, type Ref } from 'vue';

type SearchGroupData = App.Data.Search.SearchGroupData;

export const MINIMUM_QUERY_LENGTH = 2;

const DEBOUNCE_MS = 250;

/**
 * Debounced grouped suggestions for a query ref. Every keystroke aborts the
 * request in flight, so a slow response for "OKO" can never overwrite the
 * results for "OKONKWO".
 */
export function useGlobalSearch(query: Ref<string>) {
    const groups = ref<SearchGroupData[]>([]);
    const searching = ref(false);

    let controller: AbortController | null = null;
    let timeout: ReturnType<typeof setTimeout> | null = null;

    const reset = () => {
        controller?.abort();
        controller = null;

        if (timeout) {
            clearTimeout(timeout);
            timeout = null;
        }
    };

    watch(query, (value) => {
        reset();

        const trimmed = value.trim();

        if (trimmed.length < MINIMUM_QUERY_LENGTH) {
            groups.value = [];
            searching.value = false;

            return;
        }

        searching.value = true;

        timeout = setTimeout(() => {
            controller = new AbortController();

            fetch(route('search.suggestions', { q: trimmed }), {
                headers: { Accept: 'application/json' },
                signal: controller.signal,
            })
                .then((response) => (response.ok ? response.json() : { groups: [] }))
                .then((data: { groups: SearchGroupData[] }) => {
                    groups.value = data.groups ?? [];
                    searching.value = false;
                })
                .catch(() => {
                    if (controller?.signal.aborted) {
                        return;
                    }

                    groups.value = [];
                    searching.value = false;
                });
        }, DEBOUNCE_MS);
    });

    return { groups, searching, reset };
}
