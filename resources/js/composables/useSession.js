import { reactive } from "vue";

export default reactive({
  sessions: [],

  setSessions(sessions) {
    this.sessions = sessions;
  },

  getSessions() {
    return this.sessions;
  },
});
