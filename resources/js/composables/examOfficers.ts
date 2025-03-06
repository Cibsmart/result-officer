import { onMounted, ref } from 'vue';
import axios from 'axios';
import { SelectItem } from '@/types';
import ExamOfficerListData = App.Data.ExamOfficer.ExamOfficerListData;

export function useExamOfficers() {
    const isLoading = ref(false);
    const officers = ref<SelectItem[]>([{ id: 0, name: 'Loading...' }]);
    const error = ref<string | null>(null);

    const fetchExamOfficers = async () => {
        isLoading.value = true;
        error.value = null;

        try {
            const response = await axios.get<ExamOfficerListData>('/api/exam-officers');
            officers.value = response.data.data as SelectItem[];
        } catch (e: any) {
            error.value = e.message;
        } finally {
            isLoading.value = false;
        }
    };

    onMounted(fetchExamOfficers);

    return { officers, isLoading, error };
}
