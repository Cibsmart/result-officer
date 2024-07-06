<script lang="ts" setup>
import ApplicationLogo from "@/components/applicationLogo.vue";
import Navigation from "@/layouts/main/partials/navigation.vue";
import Profile from "@/layouts/main/partials/profile.vue";
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
