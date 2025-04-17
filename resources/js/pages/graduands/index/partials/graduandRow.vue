<script lang="ts" setup>
import { computed, onMounted, watch } from 'vue';
import Badge from '@/components/badge.vue';
import { PrimaryLinkSmall } from '@/components/links';
import { PrimaryButtonSmall, SecondaryButtonSmall } from '@/components/buttons';
import { BaseTD } from '@/components/tables';
import { usePoll } from '@inertiajs/vue3';

const props = defineProps<{
    student: App.Data.Graduands.GraduandData;
}>();

defineEmits<{
    (e: 'showReport', student: App.Data.Graduands.GraduandData): void;
    (e: 'showClearance', student: App.Data.Graduands.GraduandData): void;
}>();

const { start, stop } = usePoll(5000, {}, { autoStart: false });
const vetting = computed(() => props.student.vettingStatus === 'vetting');

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

const passed = computed(() => props.student.vettingStatus === 'passed');
const vetted = computed(() => props.student.vettingStatus !== 'pending');
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
        <PrimaryButtonSmall
            v-if="vetted"
            @click="$emit('showReport', student)">
            View
        </PrimaryButtonSmall>
    </BaseTD>

    <BaseTD
        mobile
        position="left">
        <SecondaryButtonSmall
            v-if="passed"
            @click="$emit('showClearance', student)">
            Clear
        </SecondaryButtonSmall>

        <PrimaryLinkSmall
            v-else
            :href="route('vetting.create', { student: student })"
            preserveScroll>
            {{ vetted ? 'Re-vet' : 'Vet' }}
        </PrimaryLinkSmall>
    </BaseTD>
</template>
