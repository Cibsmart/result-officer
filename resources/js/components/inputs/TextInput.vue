<script lang="ts" setup>
import { Input } from '@/components/ui/input';

withDefaults(
    defineProps<{
        type?: 'text' | 'email' | 'password' | 'number';
    }>(),
    {
        type: 'text',
    },
);

const model = defineModel<string | number>({ required: true });
</script>

<template>
    <Input
        v-model="model"
        :type />
</template>
