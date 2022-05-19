export default {
    state: {
        isGuest: true,
        me: {},
        appLoading: false,
        loadingText: '',
        drawer: false,
        isView: false,
        wsTest2: {
            ip: '52.199.5.88',
            port: 4649,
            tmPort: 2410
        },
        wsTest1: {
            ip: '52.199.5.88',
            port: 4650,
            tmPort: 2411
        },
        ws: {
            ip: '54.250.135.124',
            port: 4649,
            tmPort: 2410
        },
        server: '52.196.115.119',
        ev: 'official'
    },
    getters: {
        isTouchDevice(state) {
            return ('ontouchstart' in window || navigator.msMaxTouchPoints) || false
        },
        isIosDevice(state) {
            //console.log(navigator.userAgent)
            /*var u = navigator.userAgent
            return !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/)*/
            return /(Mac|iPhone|iPod|iPad)/i.test(navigator.platform) && !window.MSStream
        }
    },
    mutations: {
        setMe(state, val) {
            state.me = val
        },
        changeLoginStatus(state, val) {
            state.isGuest = val
        },
        changeAppLoadingStatus(state, val) {
            state.appLoading = val
        },
        changeLoadingText(state, val) {
            state.loadingText = val
        },
        changeDrawerStatus(state, val) {
            state.drawer = val
        },
        changePageStatus(state, val) {
            state.isView = val
        },
        setEV(state, val) {
            state.ev = val
        }
    },
    actions: {
        setMe: ({ commit }, val) => {
            commit('setMe', val)
        },
        changeTitle: ({ commit }, val) => {
            document.title = `${val}::`;
        },
        changeLoginStatus: ({ commit }, val) => {
            commit('changeLoginStatus', val)
        },
        changeAppLoadingStatus: ({ commit }, val) => {
            commit('changeAppLoadingStatus', val)
        },
        changeLoadingText: ({ commit }, val) => {
            commit('changeLoadingText', val)
        },
        changeDrawerStatus: ({ commit }, val) => {
            commit('changeDrawerStatus', val)
        },
        changePageStatus: ({ commit }, val) => {
            commit('changePageStatus', val)
        },
        setEV: ({ commit }, val) => {
            commit('setEV', val)
        }
    }
}
