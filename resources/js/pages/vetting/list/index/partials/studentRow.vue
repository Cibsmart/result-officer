<script lang="ts" setup>
import { computed } from "vue";
import PrimaryLinkSmall from "@/components/links/primaryLinkSmall.vue";
import Badge from "@/components/badge.vue";
import SecondaryButtonSmall from "@/components/buttons/secondaryButtonSmall.vue";

const props = defineProps<{
  index: number;
  student: App.Data.Vetting.VettingStudentData;
}>();

defineEmits<{
  (e: "showReport", id: number): void;
}>();

const passed = computed(() => props.student.vettingStatus === "passed");
const vetted = computed(() => props.student.vettingStatus !== "pending");
</script>

<template>
  <tr>
    <td
      class="hidden border-t border-gray-200 px-3 py-2 text-center text-sm text-gray-700 lg:table-cell dark:border-gray-700 dark:text-gray-300">
      {{ index + 1 }}
    </td>

    <td class="relative border-t border-gray-200 py-2 text-left text-sm dark:border-gray-700">
      {{ student.name }}
    </td>

    <td class="relative border-t border-gray-200 py-2 text-center text-sm dark:border-gray-700">
      {{ student.registrationNumber }}
    </td>

    <td class="relative border-t border-gray-200 py-2 text-center text-sm dark:border-gray-700">
      <Badge :color="student.vettingStatusColor">
        {{ student.vettingStatus }}
      </Badge>
    </td>

    <td
      class="border-t border-gray-200 px-3 py-2 text-center text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">
      <SecondaryButtonSmall
        v-if="vetted"
        @click="$emit('showReport', student.id)">
        view
      </SecondaryButtonSmall>

      <span v-else>N/A</span>
    </td>

    <td
      class="border-t border-gray-200 px-3 py-2 text-center text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">
      <PrimaryLinkSmall
        v-if="passed"
        :href="route('vetting.create', { student: student.id })"
        >clear
      </PrimaryLinkSmall>

      <PrimaryLinkSmall
        v-else
        :href="route('vetting.create', { student: student.id })"
        >vet
      </PrimaryLinkSmall>
    </td>
  </tr>
</template>
