<script lang="ts" setup>
import Badge from "@/components/badge.vue";
import SectionHeader from "@/components/sectionHeader.vue";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { EllipsisVerticalIcon } from "@heroicons/vue/20/solid";
</script>

<template>
  <div class="flex items-start justify-between">
    <div class="sm:w-0 sm:flex-1">
      <div class="flex items-start space-x-5">
        <div class="shrink-0">
          <div class="relative">
            <img
              alt=""
              class="size-16 rounded-full"
              src="https://images.unsplash.com/photo-1463453091185-61582044d556?ixlib=rb-=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=8&w=1024&h=1024&q=80" />

            <span
              aria-hidden="true"
              class="absolute inset-0 rounded-full shadow-inner" />
          </div>
        </div>

        <div class="pt-1">
          <SectionHeader description="EBSU/2009/51486"> Ifebude Barnabas</SectionHeader>
        </div>
      </div>
    </div>

    <div class="mt-2 flex items-center justify-between sm:ml-6 sm:shrink-0 sm:justify-start">
      <Badge color="green">Active</Badge>

      <Menu
        as="div"
        class="relative ml-3 inline-block text-left">
        <div>
          <MenuButton
            class="-my-2 flex items-center rounded-full bg-white p-2 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-200 dark:hover:text-gray-50">
            <span class="sr-only">Open options</span>

            <EllipsisVerticalIcon
              aria-hidden="true"
              class="size-5" />
          </MenuButton>
        </div>

        <transition
          enter-active-class="transition ease-out duration-100"
          enter-from-class="transform opacity-0 scale-95"
          enter-to-class="transform opacity-100 scale-100"
          leave-active-class="transition ease-in duration-75"
          leave-from-class="transform opacity-100 scale-100"
          leave-to-class="transform opacity-0 scale-95">
          <MenuItems
            class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none dark:bg-gray-900 dark:ring-white/20">
            <div class="py-1">
              <MenuItem v-slot="{ active }">
                <a
                  :class="[
                    active
                      ? 'bg-gray-100 text-gray-900 outline-none dark:bg-gray-800 dark:text-white'
                      : 'text-gray-700 dark:text-gray-200',
                  ]"
                  class="flex justify-between px-4 py-2 text-sm"
                  href="#">
                  <span>Edit</span>
                </a>
              </MenuItem>
            </div>
          </MenuItems>
        </transition>
      </Menu>
    </div>
  </div>
</template>
