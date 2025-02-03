<script lang="ts" setup>
import {computed} from "vue";

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
  <td
      :class="[mobile ? '' : 'hidden', textPosition]"
      class="relative border-t border-gray-200 py-2 text-sm lg:table-cell dark:border-gray-700">
    <slot/>
  </td>
</template>
