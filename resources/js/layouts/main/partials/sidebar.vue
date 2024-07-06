<script lang="ts" setup>
import ApplicationLogo from "@/components/applicationLogo.vue";
import Navigation from "@/layouts/main/partials/navigation.vue";
import Profile from "@/layouts/main/partials/profile.vue";
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { ChevronRightIcon } from "@heroicons/vue/20/solid";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { UserNavigationItem } from "@/types";

const pageProps = usePage();

const initials = computed(() => {
  return pageProps.props.auth.user.name
    .split(" ")
    .map((word) => word[0].toUpperCase())
    .join("");
});

const userNavigation: UserNavigationItem[] = [
  { name: "Profile", href: route("profile.edit"), method: "get" },
  { name: "Sign out", href: route("logout"), method: "post" },
];
</script>

<template>
  <div
    class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6 pb-4 dark:bg-gray-900 md:dark:ring-1 md:dark:ring-white/10">
    <div class="flex h-16 shrink-0 items-center">
      <ApplicationLogo />
    </div>

    <nav class="flex flex-1 flex-col">
      <ul
        class="flex flex-1 flex-col gap-y-7"
        role="list">
        <li>
          <Navigation />
        </li>

        <!-- Footer          -->
        <li class="mt-auto">
          <Profile />
        </li>
      </ul>
    </nav>
  </div>
</template>
