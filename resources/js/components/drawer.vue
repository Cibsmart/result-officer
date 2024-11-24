<script lang="ts" setup>
import { computed, onMounted, onUnmounted } from "vue";
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from "@headlessui/vue";
import { XMarkIcon } from "@heroicons/vue/24/outline";

const props = withDefaults(
  defineProps<{
    open?: boolean;
    size?: "normal" | "wide";
    closeable?: boolean;
    title?: string;
    sub?: string;
  }>(),
  {
    open: false,
    size: "normal",
    closeable: true,
    sub: "",
  },
);

const emit = defineEmits<(e: "close") => void>();

const close = () => {
  if (props.closeable) {
    emit("close");
  }
};

const closeOnEscape = (e: KeyboardEvent) => {
  if (e.key === "Escape" && props.open) {
    close();
  }
};

onMounted(() => document.addEventListener("keydown", closeOnEscape));

onUnmounted(() => document.removeEventListener("keydown", closeOnEscape));

const maxWidthClass = computed(() => {
  return {
    normal: "max-w-md",
    wide: "max-w-2xl",
  }[props.size];
});
</script>

<template>
  <TransitionRoot
    :show="open"
    as="template">
    <Dialog
      class="relative z-10"
      @close="close">
      <TransitionChild
        as="template"
        enter="ease-in-out duration-500"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in-out duration-500"
        leave-from="opacity-100"
        leave-to="opacity-0">
        <div class="fixed inset-0 bg-gray-500/50 transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
          <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
            <TransitionChild
              as="template"
              enter="transform transition ease-in-out duration-500 sm:duration-700"
              enter-from="translate-x-full"
              enter-to="translate-x-0"
              leave="transform transition ease-in-out duration-500 sm:duration-700"
              leave-from="translate-x-0"
              leave-to="translate-x-full">
              <DialogPanel
                :class="maxWidthClass"
                class="pointer-events-auto relative w-screen">
                <div class="flex h-full flex-col overflow-y-scroll bg-white py-6 shadow-xl dark:bg-gray-800">
                  <div class="px-4 sm:px-6">
                    <div class="flex items-start justify-between space-x-3">
                      <div class="space-y-1">
                        <DialogTitle class="text-base font-semibold text-gray-900 dark:text-white">
                          {{ title }}
                        </DialogTitle>

                        <p class="text-sm text-gray-400">
                          {{ sub }}
                        </p>
                      </div>

                      <div class="ml-3 flex h-7 items-center">
                        <button
                          class="relative rounded-md text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-100 dark:hover:text-white"
                          type="button"
                          @click="close">
                          <span class="absolute -inset-2.5" />

                          <span class="sr-only">Close panel</span>

                          <XMarkIcon
                            aria-hidden="true"
                            class="size-6" />
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="relative mt-6 flex-1 px-4 sm:px-6">
                    <slot />
                  </div>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
