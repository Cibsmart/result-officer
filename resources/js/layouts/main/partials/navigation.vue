<script lang="ts" setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import { ChevronRightIcon, ArrowDownTrayIcon } from "@heroicons/vue/20/solid";
import { NavigationItem } from "@/types";
import {
  HomeIcon,
  FolderIcon,
  ChartPieIcon,
  DocumentCheckIcon,
  AdjustmentsHorizontalIcon,
} from "@heroicons/vue/24/outline";
import { Link, usePage } from "@inertiajs/vue3";

const navigation: NavigationItem[] = [
  { name: "Dashboard", href: route("dashboard"), icon: HomeIcon, current: route().current("dashboard") },
  {
    name: "Download",
    href: "#",
    icon: ArrowDownTrayIcon,
    current: false,
    children: [
      {
        name: "Courses",
        href: route("download.courses.page"),
        current: route().current("download.courses.page"),
      },
      {
        name: "Departments",
        href: route("download.departments.page"),
        current: route().current("download.departments.page"),
      },
      {
        name: "Students",
        href: route("download.students.page", { selectedIndex: 0 }),
        current: route().current("download.students.page"),
      },
      {
        name: "Registrations",
        href: route("download.registrations.page", { selectedIndex: 0 }),
        current: route().current("download.registrations.page"),
      },
      {
        name: "Results",
        href: route("download.results.page", { selectedIndex: 0 }),
        current: route().current("download.results.page"),
      },
    ],
  },
  {
    name: "Students",
    href: "#",
    icon: FolderIcon,
    current: usePage().component.startsWith("students/"),
    children: [{ name: "View", href: route("students.show"), current: route().current("students.show") }],
  },
  {
    name: "Results",
    href: "#",
    icon: FolderIcon,
    current: usePage().component.startsWith("results/"),
    children: [
      { name: "View", href: route("results.form"), current: route().current("results.form") },
      { name: "Summary", href: route("summary.form"), current: route().current("summary.form") },
      { name: "Transcript", href: route("results.form"), current: route().current("results.form") },
    ],
  },
  {
    name: "Vetting",
    href: "#",
    icon: DocumentCheckIcon,
    current: false,
    children: [{ name: "List", href: route("vetting.index"), current: route().current("vetting.index") }],
  },
  {
    name: "Reports",
    href: "#",
    icon: ChartPieIcon,
    current: false,
    children: [
      {
        name: "Cleared",
        href: route("department.cleared.index"),
        current: route().current("department.cleared.index"),
      },
      { name: "Composite", href: route("composite.form"), current: route().current("composite.form") },
    ],
  },
];
</script>

<template>
  <ul
    class="-mx-2 space-y-1"
    role="list">
    <li
      v-for="item in navigation"
      :key="item.name">
      <Link
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
      </Link>

      <Disclosure
        v-else
        v-slot="{ open }"
        as="div">
        <DisclosureButton
          :class="[item.current ? 'bg-gray-50 dark:bg-gray-800' : 'hover:bg-gray-50 dark:hover:bg-gray-800']"
          class="flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm font-semibold leading-6 text-gray-700 dark:text-gray-400">
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
            :class="[open ? 'rotate-90 text-gray-500 dark:text-gray-300' : 'text-gray-400']"
            aria-hidden="true"
            class="ml-auto h-5 w-5 shrink-0" />
        </DisclosureButton>

        <DisclosurePanel
          as="ul"
          class="mt-1 px-2">
          <li
            v-for="subItem in item.children"
            :key="subItem.name">
            <!-- 44px -->
            <Link
              :class="[subItem.current ? 'bg-gray-50 dark:bg-gray-800' : 'hover:bg-gray-50 dark:hover:bg-gray-800']"
              :href="subItem.href"
              class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-gray-700 dark:text-gray-300">
              {{ subItem.name }}
            </Link>
          </li>
        </DisclosurePanel>
      </Disclosure>
    </li>

    <li>
      <a
        class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-700 hover:bg-gray-50 hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
        href="/admin"
        target="_blank">
        <component
          :is="AdjustmentsHorizontalIcon"
          aria-hidden="true"
          class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-white" />
        Admin
      </a>
    </li>
  </ul>
</template>
