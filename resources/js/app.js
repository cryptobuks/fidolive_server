require('./bootstrap')

import App from './views/App'
import store from './store'
import router from './router'
const app = new Vue({
    el: '#app',
    components: { App },
    router,
    store
})
