<script lang="ts" setup>
import { useStudentIndexQuery } from '@/composables/useStudentIndexQuery';
import { Search, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';

type StudentIndexFilterData = App.Data.Students.StudentIndexFilterData;
type StudentFilterOptionsData = App.Data.Students.StudentFilterOptionsData;

const props = defineProps<{
    filters: StudentIndexFilterData;
    options: StudentFilterOptionsData;
}>();

const DEBOUNCE_MS = 300;

const { visit } = useStudentIndexQuery(() => props.filters);

const term = ref(props.filters.search);

let timeout: ReturnType<typeof setTimeout> | null = null;

/** Typing waits for a pause; every other control acts on change. */
watch(term, (value) => {
    if (timeout) {
        clearTimeout(timeout);
    }

    timeout = setTimeout(() => visit({ search: value.trim() }), DEBOUNCE_MS);
});

const clear = () => {
    term.value = '';

    visit({ department: '', entrySession: '', gender: '', search: '', status: '' });
};

const selectClasses =
    'w-full rounded-md bg-white py-2 pr-8 pl-3 text-sm text-gray-900 ring-1 shadow-xs ring-gray-300 ring-inset focus:ring-2 focus:ring-indigo-600 focus:outline-hidden dark:bg-gray-900 dark:text-white dark:ring-gray-700';

const hasFilters = () =>
    props.filters.search !== '' ||
    props.filters.gender !== null ||
    props.filters.status !== null ||
    props.filters.department !== null ||
    props.filters.entrySession !== null;
</script>

<template>
    <div class="space-y-3">
        <div class="flex items-center gap-2 rounded-md border px-3 dark:border-gray-700">
            <Search class="size-4 shrink-0 text-gray-500 dark:text-gray-400" />

            <input
                v-model="term"
                aria-label="Search students by name or registration number"
                autocomplete="off"
                class="placeholder:text-muted-foreground h-10 w-full bg-transparent text-sm outline-hidden"
                placeholder="Search by name or registration number…"
                type="search" />
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label
                    class="sr-only"
                    for="filter-gender"
                    >Gender</label
                >

                <select
                    id="filter-gender"
                    :class="selectClasses"
                    :value="filters.gender ?? ''"
                    @change="visit({ gender: ($event.target as HTMLSelectElement).value })">
                    <option
                        v-for="option in options.genders"
                        :key="`gender-${option.value}`"
                        :value="option.value">
                        {{ option.label }}
                    </option>
                </select>
            </div>

            <div>
                <label
                    class="sr-only"
                    for="filter-status"
                    >Status</label
                >

                <select
                    id="filter-status"
                    :class="selectClasses"
                    :value="filters.status ?? ''"
                    @change="visit({ status: ($event.target as HTMLSelectElement).value })">
                    <option
                        v-for="option in options.statuses"
                        :key="`status-${option.value}`"
                        :value="option.value">
                        {{ option.label }}
                    </option>
                </select>
            </div>

            <div>
                <label
                    class="sr-only"
                    for="filter-department"
                    >Department</label
                >

                <select
                    id="filter-department"
                    :class="selectClasses"
                    :value="filters.department === null ? '' : String(filters.department)"
                    @change="visit({ department: ($event.target as HTMLSelectElement).value })">
                    <option
                        v-for="option in options.departments"
                        :key="`department-${option.value}`"
                        :value="option.value">
                        {{ option.label }}
                    </option>
                </select>
            </div>

            <div>
                <label
                    class="sr-only"
                    for="filter-year"
                    >Entry Year</label
                >

                <select
                    id="filter-year"
                    :class="selectClasses"
                    :value="filters.entrySession === null ? '' : String(filters.entrySession)"
                    @change="visit({ entrySession: ($event.target as HTMLSelectElement).value })">
                    <option
                        v-for="option in options.years"
                        :key="`year-${option.value}`"
                        :value="option.value">
                        {{ option.label }}
                    </option>
                </select>
            </div>
        </div>

        <div
            v-if="hasFilters()"
            class="flex justify-end">
            <button
                class="inline-flex items-center gap-1 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                type="button"
                @click="clear">
                <X class="size-3.5" />
                Clear filters
            </button>
        </div>
    </div>
</template>
