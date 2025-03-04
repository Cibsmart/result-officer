<script lang="ts" setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import { computed } from "vue";
import { ChevronRightIcon } from "@heroicons/vue/20/solid";

const props = withDefaults(defineProps<{ size?: "normal" | "wide" | "full" }>(), { size: "wide" });

const maxWidthClass = computed(() => {
  return {
    normal: "max-w-md",
    wide: "max-w-2xl",
    full: "max-w-full",
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
          class="flex w-full justify-between rounded-lg px-2 py-1.5 text-left text-sm font-medium text-black hover:bg-gray-50 focus:outline-hidden focus-visible:ring-3 focus-visible:ring-indigo-500/75 dark:text-white dark:hover:bg-gray-800">
          <slot name="header" />

          <ChevronRightIcon
            :class="open ? 'rotate-90 transform' : ''"
            class="ml-5 h-5 w-5 text-indigo-500 dark:text-white" />
        </DisclosureButton>

        <DisclosurePanel class="px-2 pt-2 pb-1 text-sm text-gray-700 dark:text-gray-200">
          <slot />
        </DisclosurePanel>
      </Disclosure>
    </div>
  </div>
</template>
