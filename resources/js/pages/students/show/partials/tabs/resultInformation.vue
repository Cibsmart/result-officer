<script lang="ts" setup>
import EmptyState from "@/components/emptyState.vue";
import IconLink from "@/components/links/iconLink.vue";
import Session from "@/pages/results/view/partials/session.vue";
import { computed } from "vue";

const props = defineProps<{
  results: App.Data.Results.StudentResultData;
}>();

const hasResults = computed(() => props.results.sessionEnrollments.length > 0);
</script>

<template>
  <div>
    <template v-if="hasResults">
      <Session
        v-for="session in results.sessionEnrollments"
        :key="session.id"
        :session="session"
        manageable />
    </template>

    <EmptyState
      v-else
      description="Get started by downloading student's results from the Portal"
      title="No Result">
      <IconLink :href="route('download.results.page')">Download Results</IconLink>
    </EmptyState>

    <div
      v-if="hasResults"
      class="mt-2 flex flex-col p-2 text-center text-xl font-bold text-black uppercase lg:block dark:text-white">
      <span>
        Current Final CGPA:
        <span>{{ results.formattedFCGPA }} </span>
      </span>

      <span class="hidden lg:inline"> (</span>

      <span class=""> {{ results.degreeClass }}</span>

      <span class="hidden lg:inline">)</span>
    </div>
  </div>
</template>
