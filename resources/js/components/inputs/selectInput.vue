<script lang="ts" setup>
import { ref } from "vue";
import { Listbox, ListboxButton, ListboxOption, ListboxOptions } from "@headlessui/vue";
import { CheckIcon, ChevronUpDownIcon } from "@heroicons/vue/20/solid";
import { SelectItem } from "@/types";

const props = defineProps<{
  items: SelectItem[];
}>();

const selected = ref(props.items[0]);
</script>

<template>
  <Listbox
    v-model="selected"
    as="div">
    <div class="relative mt-2">
      <ListboxButton
        class="relative w-full cursor-default rounded-md bg-white py-2 pr-10 pl-3 text-left text-gray-900 ring-1 shadow-xs ring-gray-300 ring-inset focus:ring-2 focus:ring-indigo-600 focus:outline-hidden sm:text-sm sm:leading-6 dark:bg-gray-900 dark:text-white dark:ring-gray-700">
        <span class="block truncate">{{ selected.name }}</span>

        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
          <ChevronUpDownIcon
            aria-hidden="true"
            class="h-5 w-5 text-gray-400" />
        </span>
      </ListboxButton>

      <transition
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0">
        <ListboxOptions
          class="ring-opacity-5 absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base ring-1 shadow-lg ring-black focus:outline-hidden sm:text-sm dark:bg-gray-900 dark:ring-gray-700">
          <ListboxOption
            v-for="item in items"
            :key="item.id"
            v-slot="{ active, selected }"
            :disabled="item.id === 0"
            :value="item"
            as="template">
            <li
              :class="[active ? 'bg-indigo-600 text-white dark:bg-gray-700' : 'text-gray-900 dark:text-white']"
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
          </ListboxOption>
        </ListboxOptions>
      </transition>
    </div>
  </Listbox>
</template>
