import { SelectItem } from "@/types";

export default {
  departments: [] as SelectItem[],

  setDepartments(departments: SelectItem[]) {
    this.departments = departments;
  },

  getDepartments(): SelectItem[] {
    return this.departments;
  },
};
