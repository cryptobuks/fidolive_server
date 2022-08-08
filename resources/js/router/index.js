import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import Home from '../views/Home'
import Login from '../views/Login'
import EquipmentList from '../views/components/equipment/List'
import AddEquipment from '../views/components/equipment/Add'
import EditEquipment from '../views/components/equipment/Edit'

import League from '../views/League'
import LeagueLive from '../views/components/league/Live'

import View from '../views/View'
import Pv from '../views/components/view/Pv'
import Lg from '../views/components/view/Lg'
import Eg from '../views/components/view/Eg'
/*
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
        }, {
            path: 'add',
            name: 'AddEquipment',
            component: AddEquipment,
            beforeEnter(to, from, next) {
                if (localStorage.getItem('id') === null) {
                    next('/')
                } else {
                    var id = Base64.decode(localStorage.getItem('id'))
                    var ids = id.split(',')
                    if (ids[2] * 1 === 1) {
                        next()
                    } else {
                        next('/')
                    }
                }
            }
        }, {
            path: 'edit',
            name: 'EditEquipment',
            component: EditEquipment,
            props: (route) => ({ id: route.query.id }),
            beforeEnter(to, from, next) {
                if (localStorage.getItem('id') === null) {
                    next('/')
                } else {
                    next()
                }
            }
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
        path: '/league',
        component: League,
        children: [{
            path: 'live',
            name: 'LeagueLive',
            component: LeagueLive,
            props: (route) => ({ id: route.query.id, groupId: route.query.groupId })
        }]
    }, {
        path: '/view',
        component: View,
        children: [{
            path: 'lg',
            name: 'Lg',
            component: Lg,
            props: (route) => ({ id: route.query.id })
        }, {
            path: 'pv',
            name: 'Pv',
            component: Pv,
            props: (route) => ({ id: route.query.id, type: route.query.type, audio: route.query.audio })
        }, {
            path: 'eg',
            name: 'Eg',
            component: Eg,
            props: (route) => ({ id: route.query.id })
        }]
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
