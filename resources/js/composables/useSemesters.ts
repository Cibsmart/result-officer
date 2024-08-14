import { SelectItem } from "@/types";

export default {
  semesters: [] as SelectItem[],

  setSemesters(semesters: SelectItem[]) {
    this.semesters = semesters;
  },

  getSemesters(): SelectItem[] {
    return this.semesters;
  },
};
