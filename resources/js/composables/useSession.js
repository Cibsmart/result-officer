import { reactive } from "vue";

export default reactive({
  session: [],

  setSessions(session) {
    this.session = session;
  },

  getSessions() {
    return this.session;
  },
});
