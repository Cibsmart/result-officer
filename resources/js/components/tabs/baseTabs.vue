<script lang="ts" setup>
import { Tab, TabGroup, TabList, TabPanels } from "@headlessui/vue";
import { computed, ref } from "vue";
import { TabItem } from "@/types";
import { router, usePage } from "@inertiajs/vue3";
import { ChevronDownIcon } from "@heroicons/vue/16/solid";

const props = defineProps<{
  tabs: TabItem[];
  selectedIndex: number;
}>();

const page = usePage();

const width = computed(() => `w-1/${props.tabs.length}`);
const indexSelected = ref(props.selectedIndex);

const updateTab = (index: number) => {
  indexSelected.value = index;

  router.get(page.url, { selectedIndex: index }, { preserveState: true, preserveScroll: true, only: ["errors"] });
};
</script>

<template>
  <div class="rounded-md border border-gray-300 dark:border-gray-600">
    <div class="grid grid-cols-1 sm:hidden">
      <select
        :value="indexSelected"
        aria-label="Select a tab"
        class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-2 pl-3 pr-8 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 dark:bg-gray-900 dark:text-white dark:ring-gray-700">
        <option
          v-for="(tab, index) in tabs"
          :key="tab.name"
          :selected="indexSelected === index"
          :value="index">
          {{ tab.name }}
        </option>
      </select>

      <ChevronDownIcon
        aria-hidden="true"
        class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end fill-gray-500 dark:fill-gray-100" />
    </div>

    <TabGroup
      :selectedIndex="indexSelected"
      @change="updateTab">
      <TabList class="hidden justify-between rounded-sm ring-1 ring-gray-300 sm:flex dark:ring-gray-600">
        <Tab
          v-for="(tab, index) in tabs"
          :key="index"
          v-slot="{ selected }"
          as="template">
          <button
            :class="[
              selected
                ? 'border-b border-b-indigo-500 font-bold text-indigo-400 dark:border-b-gray-300 dark:text-white'
                : 'text-gray-700 hover:border-b-2 hover:border-b-gray-300 dark:text-gray-300 dark:hover:border-b-gray-600',
              width,
            ]"
            class="w-full border-r border-gray-300 px-1 py-4 text-center text-sm font-medium outline-0 first:rounded-l last:rounded-r last:border-r-0 dark:border-gray-600">
            {{ tab.name }}
          </button>
        </Tab>
      </TabList>

      <TabPanels class="p-4">
        <slot />
      </TabPanels>
    </TabGroup>
  </div>
</template>
