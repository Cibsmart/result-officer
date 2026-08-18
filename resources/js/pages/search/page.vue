<script lang="ts" setup>
import AppPage from '@/components/AppPage.vue';
import EmptyState from '@/components/emptyState.vue';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { ref } from 'vue';

type SearchGroupData = App.Data.Search.SearchGroupData;
type SearchResultData = App.Data.Search.SearchResultData;

const props = defineProps<{ query: string; groups: SearchGroupData[] }>();

const term = ref(props.query);

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Search', href: route('search') }];

const submit = () => router.get(route('search'), { q: term.value.trim() }, { preserveState: true });

const open = (result: SearchResultData) => {
    if (result.external) {
        window.location.href = result.url;

        return;
    }

    router.visit(result.url);
};
</script>

<template>
    <Head title="Search" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AppPage
            :description="query ? `Results for “${query}”` : 'Search students, courses and departments'"
            title="Search">
            <Card>
                <CardContent class="space-y-6">
                    <form
                        class="flex items-center gap-2"
                        @submit.prevent="submit">
                        <div class="flex flex-1 items-center gap-2 rounded-md border px-3">
                            <Search class="size-4 shrink-0 text-gray-500 dark:text-gray-400" />

                            <input
                                v-model="term"
                                aria-label="Search"
                                autocomplete="off"
                                class="placeholder:text-muted-foreground h-10 w-full bg-transparent text-sm outline-hidden"
                                placeholder="Search students, courses, departments…"
                                type="search" />
                        </div>
                    </form>

                    <EmptyState
                        v-if="groups.length === 0"
                        :description="
                            query
                                ? `Nothing matched “${query}”. Try a registration number, surname or course code.`
                                : 'Type a registration number, surname, course code or department name.'
                        "
                        title="No results" />

                    <div
                        v-for="group in groups"
                        :key="group.key"
                        class="space-y-2">
                        <h2 class="text-muted-foreground text-xs font-medium tracking-wide uppercase">
                            {{ group.label }}
                        </h2>

                        <ul class="divide-y rounded-md border">
                            <li
                                v-for="result in group.results"
                                :key="`${group.key}-${result.id}`">
                                <button
                                    class="flex w-full items-center gap-3 px-3 py-2.5 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-800"
                                    type="button"
                                    @click="open(result)">
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
                            </li>
                        </ul>
                    </div>
                </CardContent>
            </Card>
        </AppPage>
    </AppLayout>
</template>
