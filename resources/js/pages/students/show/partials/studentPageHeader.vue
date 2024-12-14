<script lang="ts" setup>
import Badge from "@/components/badge.vue";
import SectionHeader from "@/components/sectionHeader.vue";
import HamburgerMenu from "@/components/hamburger/hamburgerMenu.vue";
import HamburgerMenuItem from "@/components/hamburger/hamburgerMenuItem.vue";
import { computed } from "vue";
import Image from "@/components/image.vue";

const props = defineProps<{
  student: App.Data.Students.StudentBasicData;
}>();

const hasImageUrl = computed(() => props.student.photoUrl !== "");
</script>

<template>
  <div class="flex items-start justify-between">
    <div class="sm:w-0 sm:flex-1">
      <div class="flex items-start space-x-5">
        <div class="shrink-0">
          <div class="relative">
            <img
              v-if="hasImageUrl"
              :src="student.photoUrl"
              alt=""
              class="size-16 rounded-full" />

            <Image :name="student.name" />

            <span
              aria-hidden="true"
              class="absolute inset-0 rounded-full shadow-inner" />
          </div>
        </div>

        <div class="pt-1">
          <SectionHeader :description="student.registrationNumber"> {{ student.name }}</SectionHeader>
        </div>
      </div>
    </div>

    <div class="mt-2 flex items-center justify-between sm:ml-6 sm:shrink-0 sm:justify-start">
      <Badge :color="student.statusColor">{{ student.status }}</Badge>

      <HamburgerMenu>
        <HamburgerMenuItem href="#">Edit</HamburgerMenuItem>

        <HamburgerMenuItem href="#">Delete</HamburgerMenuItem>
      </HamburgerMenu>
    </div>
  </div>
</template>
