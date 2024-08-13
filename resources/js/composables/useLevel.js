import { reactive } from "vue";

export default reactive({
  levels: [],

  setLevels(levels) {
    this.levels = levels;
  },

  getLevels() {
    return this.levels;
  },
});
