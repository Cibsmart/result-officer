<script lang="ts" setup>
import { computed } from "vue";
import PrimaryLinkSmall from "@/components/links/primaryLinkSmall.vue";
import Badge from "@/components/badge.vue";
import SecondaryButtonSmall from "@/components/buttons/secondaryButtonSmall.vue";
import PrimaryButtonSmall from "@/components/buttons/primaryButtonSmall.vue";
import BaseTD from "@/components/tables/baseTD.vue";

const props = defineProps<{
  index: number;
  student: App.Data.Vetting.VettingStudentData;
}>();

defineEmits<{
  (e: "showReport", student: App.Data.Vetting.VettingStudentData): void;
  (e: "showClearance", student: App.Data.Vetting.VettingStudentData): void;
}>();

const passed = computed(() => props.student.vettingStatus === "passed");
const vetted = computed(() => props.student.vettingStatus !== "pending");
</script>

<template>
  <BaseTD>{{ index + 1 }}</BaseTD>

  <BaseTD
    mobile
    position="left">
    {{ student.name }}
  </BaseTD>

  <BaseTD>{{ student.registrationNumber }}</BaseTD>

  <BaseTD>
    <Badge :color="student.vettingStatusColor">{{ student.vettingStatus }}</Badge>
  </BaseTD>

  <BaseTD mobile>
    <SecondaryButtonSmall
      v-if="vetted"
      @click="$emit('showReport', student)">
      view
    </SecondaryButtonSmall>

    <span v-else>N/A</span>
  </BaseTD>

  <BaseTD mobile>
    <PrimaryButtonSmall
      v-if="passed"
      @click="$emit('showClearance', student)">
      clear
    </PrimaryButtonSmall>

    <PrimaryLinkSmall
      v-else
      :href="route('vetting.create', { student: student })">
      vet
    </PrimaryLinkSmall>
  </BaseTD>
</template>
