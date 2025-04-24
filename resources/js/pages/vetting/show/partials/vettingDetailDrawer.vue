<script lang="ts" setup>
import Drawer from '@/components/drawer.vue';
import Disclosure from '@/components/baseDisclosure.vue';
import { ref, computed, watch } from 'vue';
import { useVettingSteps } from '@/composables/vettingSteps';
import Badge from '@/components/badge.vue';
import { Card, CardContent, CardHeader } from '@/components/ui/card';

const props = defineProps<{
    slug: string;
    openReportDrawer: boolean;
}>();

const emit = defineEmits<(e: 'close') => void>();

const { steps, fetchVettingSteps, isLoading } = useVettingSteps();

const openDrawer = ref(false);
watch(
    () => props.openReportDrawer,
    () => {
        openDrawer.value = props.openReportDrawer;
        fetchVettingSteps(props.slug);
    },
);

const title = computed(() => `Vetting Report for ${props.slug}`);

const handleClose = () => {
    openDrawer.value = false;
    emit('close');
};
</script>

<template>
    <Drawer
        :show="openDrawer"
        :title="title"
        size="medium"
        sub="Vetting Report Details"
        @close="handleClose">
        <template v-if="!isLoading">
            <div
                v-for="vettingStep in steps"
                :key="vettingStep.id">
                <Disclosure class="mt-2">
                    <template #header>
                        <div class="flex flex-1 justify-between text-sm font-black">
                            <span>{{ vettingStep.title }}</span>

                            <Badge :color="vettingStep.color">
                                {{ vettingStep.status }}
                            </Badge>
                        </div>
                    </template>

                    <Card>
                        <CardHeader>{{ vettingStep.description }}</CardHeader>

                        <CardContent>
                            <ul
                                class="list-inside list-decimal divide-y divide-gray-300 dark:divide-gray-700"
                                role="list">
                                <li
                                    v-for="report in vettingStep.reports"
                                    :key="report.id"
                                    class="p-2">
                                    {{ report.message }}
                                </li>
                            </ul>
                        </CardContent>
                    </Card>
                </Disclosure>
            </div>
        </template>

        <template v-else> Loading...</template>
    </Drawer>
</template>
