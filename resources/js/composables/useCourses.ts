import { SelectItem } from "@/types";

export default {
  courses: [] as SelectItem[],

  setCourses(courses: SelectItem[]) {
    this.courses = courses;
  },

  getCourses(): SelectItem[] {
    return this.courses;
  },
};
