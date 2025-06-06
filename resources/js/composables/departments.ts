import { onMounted, ref } from 'vue';
import axios from 'axios';
import { SelectItem } from '@/types';
import DepartmentListData = App.Data.Department.DepartmentListData;

export function useDepartments(userDepartmentOnly = false) {
    const isLoading = ref(false);
    const departments = ref<SelectItem[]>([{ id: 0, name: 'Loading...' }]);
    const error = ref<string | null>(null);
    const url = userDepartmentOnly ? '/api/user-departments' : '/api/departments';

    const fetchDepartments = async () => {
        isLoading.value = true;
        error.value = null;

        try {
            const response = await axios.get<DepartmentListData>(url);
            departments.value = response.data.data as SelectItem[];
        } catch (e: any) {
            error.value = e.message;
        } finally {
            isLoading.value = false;
        }
    };

    onMounted(fetchDepartments);

    return { departments, isLoading, error };
}
