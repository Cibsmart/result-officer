<script lang="ts" setup>
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { Link } from "@inertiajs/vue3";
import { ChevronDownIcon } from "@heroicons/vue/20/solid";
import { UserNavigationItem } from "@/types";

const userNavigation: UserNavigationItem[] = [
  { name: "Your profile", href: route("profile.edit"), method: "get" },
  { name: "Sign out", href: route("logout"), method: "post" },
];
</script>

<template>
  <Menu
    as="div"
    class="relative">
    <MenuButton class="-m-1.5 flex items-center p-1.5">
      <span class="sr-only">Open user menu</span>

      <img
        alt=""
        class="h-8 w-8 rounded-full bg-gray-50 dark:bg-gray-800 dark:text-white"
        src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" />

      <span class="hidden lg:flex lg:items-center">
        <span
          aria-hidden="true"
          class="ml-4 text-sm font-semibold leading-6 text-gray-900 dark:text-white">
          {{ $page.props.auth.user.name }}
        </span>

        <ChevronDownIcon
          aria-hidden="true"
          class="ml-2 h-5 w-5 text-gray-400" />
      </span>
    </MenuButton>

    <Transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95">
      <MenuItems
        class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none dark:bg-gray-900">
        <MenuItem
          v-for="item in userNavigation"
          :key="item.name"
          v-slot="{ active }">
          <Link
            :class="[active ? 'bg-gray-50 dark:bg-gray-800' : '']"
            :href="item.href"
            :method="item.method"
            as="button"
            class="block px-3 py-1 text-sm leading-6 text-gray-900 dark:text-white">
            {{ item.name }}
          </Link>
        </MenuItem>
      </MenuItems>
    </Transition>
  </Menu>
</template>
