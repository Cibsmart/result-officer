import { SelectItem } from "@/types";

export default {
  sessions: [] as SelectItem[],

  setSessions(sessions: SelectItem[]) {
    this.sessions = sessions;
  },

  getSessions(): SelectItem[] {
    return this.sessions;
  },
};
