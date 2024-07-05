<script lang="ts" setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import { ChevronRightIcon } from "@heroicons/vue/20/solid";
import { NavigationItem } from "@/types";
import { ChartPieIcon, DocumentDuplicateIcon, FolderIcon, HomeIcon, UsersIcon } from "@heroicons/vue/24/outline";

const navigation: NavigationItem[] = [
  { name: "Dashboard", href: "#", icon: HomeIcon, current: true },
  {
    name: "Students",
    href: "#",
    icon: UsersIcon,
    current: false,
    children: [
      { name: "GraphQL API", href: "#", current: false },
      { name: "iOS App", href: "#", current: false },
      { name: "Android App", href: "#", current: false },
      { name: "New Customer Portal", href: "#", current: false },
    ],
  },
  { name: "Results", href: "#", icon: FolderIcon, current: false },
  { name: "Documents", href: "#", icon: DocumentDuplicateIcon, current: false },
  { name: "Reports", href: "#", icon: ChartPieIcon, current: false },
];
</script>

<template>
  <ul
    class="-mx-2 space-y-1"
    role="list">
    <li
      v-for="item in navigation"
      :key="item.name">
      <a
        v-if="!item.children"
        :class="[
          item.current
            ? 'bg-gray-50 text-indigo-600 dark:bg-gray-800 dark:text-white'
            : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white',
        ]"
        :href="item.href"
        class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6">
        <component
          :is="item.icon"
          :class="[
            item.current
              ? 'text-indigo-600 dark:text-white'
              : 'text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-white',
          ]"
          aria-hidden="true"
          class="h-6 w-6 shrink-0" />
        {{ item.name }}
      </a>

      <Disclosure
        v-else
        v-slot="{ open }"
        as="div">
        <DisclosureButton
          class="flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm font-semibold leading-6 text-gray-700 dark:text-gray-400"
          :class="[item.current ? 'bg-gray-50 dark:bg-gray-800' : 'hover:bg-gray-50 dark:hover:bg-gray-800']">
          <component
            :is="item.icon"
            :class="[
              item.current
                ? 'text-indigo-600 dark:text-white'
                : 'text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-white',
            ]"
            aria-hidden="true"
            class="h-6 w-6 shrink-0 text-gray-400" />
          {{ item.name }}
          <ChevronRightIcon
            class="ml-auto h-5 w-5 shrink-0"
            :class="[open ? 'rotate-90 text-gray-500 dark:text-gray-300' : 'text-gray-400']"
            aria-hidden="true" />
        </DisclosureButton>

        <DisclosurePanel
          as="ul"
          class="mt-1 px-2">
          <li
            v-for="subItem in item.children"
            :key="subItem.name">
            <!-- 44px -->
            <DisclosureButton
              class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-gray-700 dark:text-gray-300"
              :class="[subItem.current ? 'bg-gray-50 dark:bg-gray-800' : 'hover:bg-gray-50 dark:hover:bg-gray-800']"
              :href="subItem.href"
              as="a">
              {{ subItem.name }}
            </DisclosureButton>
          </li>
        </DisclosurePanel>
      </Disclosure>
    </li>
  </ul>
</template>
