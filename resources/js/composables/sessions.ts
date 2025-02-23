import { onMounted, ref } from "vue";
import axios from "axios";
import { SelectItem } from "@/types";
import SessionListData = App.Data.Session.SessionListData;

export function useSessions() {
  const isLoading = ref(false);
  const sessions = ref<SelectItem[]>([{ id: 0, name: "Loading..." }]);
  const error = ref<string | null>(null);

  const fetchSessions = async () => {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await axios.get<SessionListData>("/api/sessions");
      sessions.value = response.data.sessions as SelectItem[];
    } catch (e: any) {
      error.value = e.message;
    } finally {
      isLoading.value = false;
    }
  };

  onMounted(fetchSessions);

  return { sessions, isLoading, error };
}
