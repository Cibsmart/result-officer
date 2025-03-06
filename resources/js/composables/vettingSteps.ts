import { ref } from 'vue';
import axios from 'axios';
import VettingStepListData = App.Data.Vetting.VettingStepListData;
import VettingStepData = App.Data.Vetting.VettingStepData;

export function useVettingSteps() {
    const steps = ref<VettingStepData[]>();
    const isLoading = ref(false);
    const error = ref<string | null>(null);

    const fetchVettingSteps = async (studentSlug: string) => {
        isLoading.value = true;
        error.value = null;

        try {
            const response = await axios.get<VettingStepListData>(`/api/student/${studentSlug}/vetting-steps`);
            steps.value = response.data.items;
        } catch (e: any) {
            error.value = e.message;
        } finally {
            isLoading.value = false;
        }
    };

    return { steps, fetchVettingSteps, isLoading, error };
}
