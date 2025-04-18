<script lang="ts" setup>
import { Input } from '@/components/ui/input';

const model = defineModel<string | number>({ required: true });
</script>

<template>
    <Input
        v-model="model"
        disabled />
</template>
