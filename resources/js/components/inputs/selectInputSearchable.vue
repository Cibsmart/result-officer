<script lang="ts" setup>
import { ref, computed, watch } from 'vue';
import { Combobox, ComboboxInput, ComboboxOptions, ComboboxOption, ComboboxButton } from '@headlessui/vue';
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid';
import { SelectItem } from '@/types';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    name: string;
    items: SelectItem[];
}>();

const selected = ref(props.items[0]);
const query = ref('');

const filtered = computed(() =>
    query.value === ''
        ? props.items
        : props.items.filter((item) => {
              return item.name.toLowerCase().includes(query.value.toLowerCase());
          }),
);

const empty = computed(() => filtered.value.length === 0 && query.value !== '');

watch(
    () => empty.value,
    () => router.reload({ data: { search: query.value }, only: [props.name], replace: true }),
);
</script>

<template>
    <Combobox
        v-model="selected"
        as="div"
        by="id">
        <div class="relative">
            <div
                class="relative w-full cursor-default overflow-hidden rounded-lg bg-white text-left shadow-md focus:outline-hidden focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-teal-300 sm:text-sm">
                <ComboboxInput
                    :displayValue="(item: any) => item.name"
                    autocomplete="off"
                    class="w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                    @change="query = $event.target.value" />

                <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
                    <ChevronUpDownIcon
                        aria-hidden="true"
                        class="h-5 w-5 text-gray-400" />
                </ComboboxButton>
            </div>

            <transition
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
                @after-leave="query = ''">
                <ComboboxOptions
                    class="ring-opacity-5 absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base ring-1 shadow-lg ring-black focus:outline-hidden sm:text-sm dark:bg-gray-900 dark:ring-gray-700">
                    <div
                        v-if="empty"
                        class="relative cursor-default px-4 py-2 text-gray-700 select-none">
                        Nothing found.
                    </div>

                    <ComboboxOption
                        v-for="item in filtered"
                        :key="item.id"
                        v-slot="{ active, selected }"
                        :disabled="item.id === 0"
                        :value="item"
                        as="template">
                        <li
                            :class="[
                                active ? 'bg-indigo-600 text-white dark:bg-gray-700' : 'text-gray-900 dark:text-white',
                            ]"
                            class="relative cursor-default py-2 pr-4 pl-8 select-none">
                            <span
                                :class="[selected ? 'font-semibold' : 'font-normal', item.id === 0 ? 'opacity-75' : '']"
                                class="block truncate">
                                {{ item.name }}
                            </span>

                            <span
                                v-if="selected"
                                :class="[active ? 'text-white' : 'text-indigo-600 dark:text-white']"
                                class="absolute inset-y-0 left-0 flex items-center pl-1.5">
                                <CheckIcon
                                    aria-hidden="true"
                                    class="h-5 w-5" />
                            </span>
                        </li>
                    </ComboboxOption>
                </ComboboxOptions>
            </transition>
        </div>
    </Combobox>
</template>
