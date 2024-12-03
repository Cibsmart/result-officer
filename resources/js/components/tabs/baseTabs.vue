<script lang="ts" setup>
import { Tab, TabGroup, TabList, TabPanels } from "@headlessui/vue";
import { computed, ref } from "vue";
import { TabItem } from "@/types";
import { router, usePage } from "@inertiajs/vue3";

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
  <TabGroup
    :selectedIndex="indexSelected"
    @change="updateTab">
    <TabList class="flex justify-between rounded ring-1 ring-gray-300 dark:ring-0">
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
          class="w-full border-r border-gray-300 px-1 py-4 text-center text-sm font-medium outline-0 first:rounded-l last:rounded-r last:border-r-0 dark:border-gray-700 dark:bg-gray-900">
          {{ tab.name }}
        </button>
      </Tab>
    </TabList>

    <TabPanels>
      <slot />
    </TabPanels>
  </TabGroup>
</template>
