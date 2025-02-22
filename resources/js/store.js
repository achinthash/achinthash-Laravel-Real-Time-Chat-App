import { createStore } from 'vuex';

export default createStore({
  state: {
    selectedGroup: null, // This will hold the selected group data
    selectedPrivate: null // FIXED: Typo corrected from "selecetedPrivate" to "selectedPrivate"
  },
  mutations: {
    setSelectedGroup(state, group) {
      state.selectedGroup = group; 
      state.selectedPrivate = null;
    },

    setSelectedPrivate(state, chat) {
      state.selectedPrivate = chat; // FIXED: Typo corrected here
      state.selectedGroup = null;
    },
  },
  actions: {
    selectGroup({ commit }, group) {
      commit('setSelectedGroup', group); 
    },
    selectChat({ commit }, chat) {
      commit('setSelectedPrivate', chat); // FIXED: Commit to the corrected mutation
    },
  },
  getters: {
    selectedGroup(state) {
      return state.selectedGroup; 
    },
    selectedPrivate(state) { // FIXED: Typo corrected here
      return state.selectedPrivate;
    },
  },
});
