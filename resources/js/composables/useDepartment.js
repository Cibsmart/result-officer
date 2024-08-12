import { reactive } from "vue";

export default reactive({
  departments: [],

  setDepartments(departments) {
    this.departments = departments;
  },

  getDepartments() {
    return this.departments;
  },
});
