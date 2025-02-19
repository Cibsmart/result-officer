<script lang="ts" setup>
import { computed, onMounted, watch } from "vue";
import PrimaryLinkSmall from "@/components/links/primaryLinkSmall.vue";
import Badge from "@/components/badge.vue";
import SecondaryButtonSmall from "@/components/buttons/secondaryButtonSmall.vue";
import PrimaryButtonSmall from "@/components/buttons/primaryButtonSmall.vue";
import BaseTD from "@/components/tables/baseTD.vue";
import { usePoll } from "@inertiajs/vue3";

const props = defineProps<{
  student: App.Data.Vetting.VettingStudentData;
}>();

defineEmits<{
  (e: "showReport", student: App.Data.Vetting.VettingStudentData): void;
  (e: "showClearance", student: App.Data.Vetting.VettingStudentData): void;
}>();

const { start, stop } = usePoll(10000, {}, { autoStart: false });
const vetting = computed(() => props.student.vettingStatus === "vetting");

onMounted(() => {
  if (vetting.value) {
    start();
  }
});

watch(vetting, () => {
  if (vetting.value) {
    start();
  } else {
    stop();
  }
});

const passed = computed(() => props.student.vettingStatus === "passed");
const vetted = computed(() => props.student.vettingStatus !== "pending");
</script>

<template>
  <BaseTD
    mobile
    position="left">
    {{ student.name }}
  </BaseTD>

  <BaseTD position="left">{{ student.registrationNumber }}</BaseTD>

  <BaseTD position="left">
    <Badge
      :class="vetting ? 'animate-pulse' : ''"
      :color="student.vettingStatusColor">
      {{ student.vettingStatus }}
    </Badge>
  </BaseTD>

  <BaseTD
    mobile
    position="left">
    <SecondaryButtonSmall
      v-if="vetted"
      @click="$emit('showReport', student)">
      view
    </SecondaryButtonSmall>

    <span v-else>N/A</span>
  </BaseTD>

  <BaseTD
    mobile
    position="left">
    <PrimaryButtonSmall
      v-if="passed"
      @click="$emit('showClearance', student)">
      clear
    </PrimaryButtonSmall>

    <PrimaryLinkSmall
      v-else
      :href="route('vetting.create', { student: student })"
      preserve-scroll>
      {{ vetted ? "re-vet" : "vet" }}
    </PrimaryLinkSmall>
  </BaseTD>
</template>
