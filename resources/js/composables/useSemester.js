import { reactive } from "vue";

export default reactive({
  semesters: [],

  setSemesters(semesters) {
    this.semesters = semesters;
  },

  getSemesters() {
    return this.semesters;
  },
});
