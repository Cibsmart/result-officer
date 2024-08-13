import { reactive } from "vue";

export default reactive({
  courses: [],

  setCourses(courses) {
    this.courses = courses;
  },

  getCourses() {
    return this.courses;
  },
});
