<script lang="ts" setup>
import { CheckIcon, XMarkIcon, ExclamationCircleIcon } from "@heroicons/vue/20/solid";

defineProps<{
  events: Array<App.Data.Import.ImportEventData>;
}>();

const settings = (status: string) => {
  if (status === "completed") {
    return { class: "bg-green-400", icon: CheckIcon };
  }
  if (status === "failed") {
    return { class: "bg-red-400", icon: XMarkIcon };
  }

  return { class: "bg-gray-400", icon: ExclamationCircleIcon };
};
</script>

<template>
  <div class="flow-root">
    <ul
      class="-mb-8"
      role="list">
      <li
        v-for="(event, eventIdx) in events"
        :key="event.id">
        <div class="relative pb-3">
          <span
            v-if="eventIdx !== events.length - 1"
            aria-hidden="true"
            class="absolute top-2 left-2 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-500" />

          <div class="relative flex space-x-2">
            <div>
              <span
                :class="settings(event.status).class"
                class="flex h-4 w-4 items-center justify-center rounded-full ring-4 ring-white dark:ring-gray-800">
                <component
                  :is="settings(event.status).icon"
                  aria-hidden="true"
                  class="h-3 w-3 text-white" />
              </span>
            </div>

            <div class="flex min-w-0 flex-1 justify-between space-x-2">
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                  <span class="block">
                    <span class="font-medium text-gray-900 dark:text-gray-300">{{ event.target }}</span>
                    {{ event.content }}
                  </span>
                  {{ event.description }}
                </p>
              </div>

              <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                {{ event.date }}
              </div>
            </div>
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>
