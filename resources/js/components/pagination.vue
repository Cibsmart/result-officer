<script lang="ts" setup>
import { Link } from '@inertiajs/vue3';
import { PaginatedData } from '@/types/paginate';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid';

defineProps<{
    paginated: PaginatedData;
}>();
</script>

<template>
    <div class="flex w-full items-center justify-between px-4 py-3 sm:px-6">
        <div class="flex flex-1 justify-between md:hidden">
            <Link
                :href="paginated.prev_page_url"
                class="relative inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-600">
                Previous
            </Link>

            <Link
                :href="paginated.next_page_url"
                class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-600">
                Next
            </Link>
        </div>

        <div class="hidden md:flex md:flex-1 md:items-center md:justify-between">
            <div>
                <p class="text-sm text-gray-700 dark:text-gray-200">
                    Showing
                    {{ ' ' }}
                    <span class="font-medium">{{ paginated.from }}</span>
                    {{ ' ' }}
                    to
                    {{ ' ' }}
                    <span class="font-medium">{{ paginated.to }}</span>
                    {{ ' ' }}
                    of
                    {{ ' ' }}
                    <span class="font-medium">{{ paginated.total }}</span>
                    {{ ' ' }}
                    results
                </p>
            </div>

            <div>
                <nav
                    aria-label="Pagination"
                    class="isolate inline-flex -space-x-px rounded-md shadow-xs">
                    <Link
                        v-for="(link, index) in paginated.links"
                        :key="index"
                        :class="{
                            'z-10 bg-indigo-600 text-white focus-visible:outline-offset-2 focus-visible:outline-indigo-600':
                                link.active,
                            'text-gray-900 ring-1 ring-gray-300 ring-inset hover:bg-gray-50 focus:outline-offset-0 dark:text-gray-100 dark:ring-gray-400 dark:hover:bg-gray-600':
                                !link.active,
                        }"
                        :href="link.url"
                        class="relative inline-flex items-center px-4 py-2 first-of-type:rounded-l-md first-of-type:px-2 last-of-type:rounded-r-md last-of-type:px-2">
                        <ChevronLeftIcon
                            v-if="index === 0"
                            aria-hidden="true"
                            class="size-5" />

                        <ChevronRightIcon
                            v-else-if="link.label.startsWith('Next')"
                            aria-hidden="true"
                            class="size-5" />

                        <template v-else>{{ link.label }}</template>
                    </Link>
                </nav>
            </div>
        </div>
    </div>
</template>
