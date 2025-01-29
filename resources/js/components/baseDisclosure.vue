<script lang="ts" setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import { computed } from "vue";
import { ChevronUpIcon } from "@heroicons/vue/20/solid";
import Badge from "@/components/badge.vue";

const props = withDefaults(
  defineProps<{
    title: string;
    badge?: string;
    color?: App.Enums.StatusColor;
    size?: "normal" | "wide";
  }>(),
  {
    color: "gray",
    size: "wide",
  },
);

const maxWidthClass = computed(() => {
  return {
    normal: "max-w-md",
    wide: "max-w-2xl",
  }[props.size];
});
</script>

<template>
  <div class="w-full">
    <div
      :class="maxWidthClass"
      class="mx-auto w-full rounded-sm bg-gray-100 p-2 dark:bg-gray-900">
      <Disclosure v-slot="{ open }">
        <DisclosureButton
          class="flex w-full justify-between rounded-lg bg-indigo-100 px-2 py-1.5 text-left text-sm font-medium text-indigo-900 hover:bg-indigo-200 focus:outline-hidden focus-visible:ring-3 focus-visible:ring-indigo-500/75 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700">
          <div class="flex flex-1 justify-between text-sm font-black">
            <span>{{ title }}</span>

            <Badge
              v-if="badge"
              :color="color">
              {{ badge }}
            </Badge>
          </div>

          <ChevronUpIcon
            :class="open ? 'rotate-180 transform' : ''"
            class="ml-5 h-5 w-5 text-indigo-500 dark:text-white" />
        </DisclosureButton>

        <DisclosurePanel class="px-2 pt-2 pb-1 text-sm text-gray-700 dark:text-gray-200">
          <slot />
        </DisclosurePanel>
      </Disclosure>
    </div>
  </div>
</template>
