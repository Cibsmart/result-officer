<script lang="ts" setup>
import type { HTMLAttributes } from 'vue';

defineProps<Props>();

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}
</script>

<template>
    <svg
        :class="className"
        v-bind="$attrs"
        viewBox="0 0 20 20"
        xmlns="http://www.w3.org/2000/svg">
        <text
            clip-rule="evenodd"
            dominant-baseline="middle"
            fill="currentColor"
            fill-rule="evenodd"
            font-size="16"
            text-anchor="middle"
            text-rendering="auto"
            textLength="100%"
            x="50%"
            y="50%">
            RDB
        </text>
    </svg>
</template>
