import { SelectItem } from "@/types";

export default {
  levels: [] as SelectItem[],

  setLevels(levels: SelectItem[]) {
    this.levels = levels;
  },

  getLevels(): SelectItem[] {
    return this.levels;
  },
};
