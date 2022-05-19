export default {
    state: {
        cont: {}
    },
    getters: {

    },
    mutations: {
    	setLangCont(state, val) {
    		state.cont = JSON.parse(val)
    	}
    },
    actions: {
    	setLangCont: ({commit}, val) => {
            commit('setLangCont', val)
        }
    }
}