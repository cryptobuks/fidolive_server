import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import Home from '../views/Home'
import Login from '../views/Login'
import EquipmentList from '../views/components/equipment/List'
/*import AddTournament from '../views/components/tournament/Add'
import EditTournament from '../views/components/tournament/Edit'
import TournamentLive from '../views/components/tournament/Live'*/
/*import Tournament from '../views/Tournament'
import League from '../views/League'
import View from '../views/View'

import TournamentList from '../views/components/tournament/List'
import AddTournament from '../views/components/tournament/Add'
import EditTournament from '../views/components/tournament/Edit'
import TournamentLive from '../views/components/tournament/Live'

import LeagueLive from '../views/components/league/Live'


import Tm1 from '../views/components/view/Tm1'
import Tm from '../views/components/view/Tm'
import Audio from '../views/components/view/Audio'
import Eg from '../views/components/view/Eg'
import Bracket from '../views/components/view/Bracket'
import Lg from '../views/components/view/Lg'
import Pv from '../views/components/view/Pv'*/

//不允許導航到當前位置
const originalPush = VueRouter.prototype.push
VueRouter.prototype.push = function push(location, onResolve, onReject) {
    if (onResolve || onReject) return originalPush.call(this, location, onResolve, onReject);
    return originalPush.call(this, location).catch(err => err)
}


export default new VueRouter({
    mode: 'history',
    routes: [{
        path: '/',
        //name: 'Home',
        component: Home,
        beforeEnter(to, from, next) {
            if (localStorage.getItem('id') === null) {
                next('/login')
            } else {
                next()
            }
        },
        children: [{
            path: '',
            name: 'EquipmentList',
            component: EquipmentList
        }]
    }, {
        path: '/login',
        name: 'Login',
        component: Login,
        beforeEnter(to, from, next) {
            if (localStorage.getItem('id') !== null) {
                next('/')
            } else {
                next()
            }
        }
    }, {
        path: "*",
        redirect: "/"
    }],
    /*
    切換路由scroll top 回頂部
     */
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        } else {
            return { x: 0, y: 0 }
        }
    }
})
