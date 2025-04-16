<script lang="ts" setup>
import StaticFeeds from '@/components/feeds/staticFeeds.vue';
import ActiveFeeds from '@/components/feeds/activeFeeds.vue';
import { computed, onMounted, watch } from 'vue';
import { usePoll, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    events: Array<App.Data.Imports.ImportEventData>;
    pending: App.Data.Imports.PendingImportEventData;
}>();

const hasPendingEvent = computed(() => props.pending !== null);
const hasEvent = computed(() => props.events.length > 0);
const pendingDescription = computed(() => (hasPendingEvent.value ? `Pending ${props.pending.type} Download` : ''));
const historyDescription = computed(() => (hasEvent.value ? `Recent ${props.events[0].type} Download History` : ''));
const { start, stop } = usePoll(5000, { only: ['pending', 'events'] }, { autoStart: false });

onMounted(() => {
    if (hasPendingEvent.value) {
        start();
    }
});

watch(hasPendingEvent, () => {
    if (hasPendingEvent.value) {
        start();
    } else {
        stop();
    }
});
</script>

<template>
    <template v-if="hasPendingEvent">
        <Card class="py-6">
            <CardDescription>{{ pendingDescription }}</CardDescription>

            <CardContent>
                <ActiveFeeds :data="pending" />
            </CardContent>

            <CardFooter>
                <div class="flex space-x-4">
                    <Button
                        asChild
                        variant="secondary">
                        <Link :href="route('import.event.cancel', { event: pending.id })"> Cancel</Link>
                    </Button>

                    <Button
                        v-if="pending.canBeContinued"
                        asChild>
                        <Link :href="route('import.event.continue', { event: pending.id })"> Continue</Link>
                    </Button>
                </div>
            </CardFooter>
        </Card>
    </template>

    <template v-if="hasEvent">
        <Card class="pt-4 pb-8">
            <CardDescription>{{ historyDescription }}</CardDescription>

            <CardContent>
                <StaticFeeds :events="events" />
            </CardContent>
        </Card>
    </template>
</template>
