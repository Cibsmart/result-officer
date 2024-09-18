import { router } from "@inertiajs/vue3";
import { ComputedRef, onBeforeUnmount, onMounted } from "vue";

export function usePoll(
  startOnMount: ComputedRef<boolean>,
  data: Array<string> = [],
  timeout: number = 1000,
): {
  start: () => void;
  stop: () => void;
} {
  let interval: ReturnType<typeof setInterval>;

  const reloadData = () => router.reload({ only: data, replace: true });

  const start = () => (interval = setInterval(() => reloadData(), timeout));

  const stop = () => clearInterval(interval);

  onMounted(() => startOnMount.value && start());

  onBeforeUnmount(() => stop());

  return { start, stop };
}
