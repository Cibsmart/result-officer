<script lang="ts" setup>
import { Textarea } from '@/components/ui/textarea';

const model = defineModel<string>({ required: true });
</script>

<template>
    <Textarea v-model="model" />
</template>
