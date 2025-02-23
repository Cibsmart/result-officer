import { onMounted, ref } from "vue";
import axios from "axios";
import { SelectItem } from "@/types";
import StateListData = App.Data.States.StateListData;

export function useStates() {
  const isLoading = ref(false);
  const states = ref<SelectItem[]>([{ id: 0, name: "Loading..." }]);
  const error = ref<string | null>(null);

  const fetchStates = async () => {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await axios.get<StateListData>("/api/states");
      states.value = response.data.data as SelectItem[];
    } catch (e: any) {
      error.value = e.message;
    } finally {
      isLoading.value = false;
    }
  };

  onMounted(fetchStates);

  return { states, isLoading, error };
}
