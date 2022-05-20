export default {
    state: {
        cont: {}
    },
    getters: {

    },
    mutations: {
    	setLangCont(state, val) {
    		state.cont = JSON.parse(val)
    	},
        setWebTitle(state, val) {
            console.log(val)
            var pageName = state.cont.pageName
            var title = 'FIDO LIVE'
            if(pageName.title) {
                title = pageName.title[val.title]? pageName.title[val.title] : title
            }
            var name = pageName[val.name]? pageName[val.name] : val.name
            document.title = `${title}::${name}`
        }
    },
    actions: {
    	setLangCont: ({commit}, val) => {
            commit('setLangCont', val)
        },
        changeTitle: ({ commit }, val) => {
            commit('setWebTitle', val)
        },
    }
}