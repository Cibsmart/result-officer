<script lang="ts" setup>
import { computed } from "vue";

const props = withDefaults(
  defineProps<{
    position?: "center" | "right" | "left";
    mobile?: true | false;
  }>(),
  {
    position: "center",
    mobile: false,
  },
);

const textPosition = computed(() => {
  return {
    center: "text-center",
    left: "text-left pl-2",
    right: "text-right pr-2",
  }[props.position];
});
</script>

<template>
  <th
    :class="[mobile ? '' : 'hidden', textPosition]"
    class="py-2 text-sm font-semibold text-gray-900 lg:table-cell dark:text-white"
    scope="col">
    <slot />
  </th>
</template>
