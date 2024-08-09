<script lang="ts" setup>
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { Link, usePage } from "@inertiajs/vue3";
import { ChevronRightIcon } from "@heroicons/vue/20/solid";
import { UserNavigationItem } from "@/types";
import ProfileImage from "@/layouts/main/partials/profileImage.vue";

const user = usePage().props.user;

const userNavigation: UserNavigationItem[] = [
  { name: "Your profile", href: route("profile.edit"), method: "get" },
  { name: "Sign out", href: route("logout"), method: "post" },
];
</script>

<template>
  <Menu
    as="div"
    class="relative">
    <MenuButton
      v-slot="{ open }"
      class="group -m-1.5 flex w-full items-center gap-x-4 p-1.5 py-3 text-sm font-semibold leading-6 text-gray-900 hover:bg-gray-50 dark:text-white dark:hover:bg-gray-800">
      <span class="sr-only">Open user menu</span>

      <ProfileImage />

      <span class="sr-only">Your profile</span>

      <span aria-hidden="true">{{ user.name }}</span>

      <ChevronRightIcon
        :class="[open ? '-rotate-90 text-gray-500 dark:text-gray-300' : 'text-gray-400']"
        aria-hidden="true"
        class="ml-auto h-5 w-5 text-gray-400" />
    </MenuButton>

    <Transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95">
      <MenuItems
        class="absolute bottom-12 right-0 z-10 mt-2.5 w-40 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none dark:bg-gray-900">
        <MenuItem
          v-for="item in userNavigation"
          :key="item.name"
          v-slot="{ active }">
          <Link
            :class="[active ? 'bg-gray-50 dark:bg-gray-800' : '']"
            :href="item.href"
            :method="item.method"
            as="button"
            class="block w-full px-3 py-1 text-right text-sm leading-6 text-gray-900 dark:text-white">
            {{ item.name }}
          </Link>
        </MenuItem>
      </MenuItems>
    </Transition>
  </Menu>
</template>
