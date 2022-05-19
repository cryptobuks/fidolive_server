<template>
    <div>
        <!-- 側邊欄菜單區域 -->
        <el-menu unique-opened :collapse="isCollapse" router :default-active="$router.currentRoute.path" :unique-opened="true" @select="changeDrawerStatus(false)">
            <div class="toggle-button hidden-sm-and-down" @click="isCollapse = !isCollapse">|||</div>
            <el-submenu index="1">
                <template slot="title">
                    <i class="el-icon-setting"></i>
                    <span slot="title">
                        {{ $store.state.langData.cont.slideMenu.Equipment }}
                    </span>
                </template>
                <el-menu-item index="/">
                    <i class="el-icon-thirdlist"></i>
                    <span slot="title">
                        {{ $store.state.langData.cont.slideMenu.List }}
                    </span>
                </el-menu-item>
            </el-submenu>
            <el-submenu index="2">
                <template slot="title">
                    <i class="el-icon-thirdtournament"></i>
                    <span slot="title">
                        {{ $store.state.langData.cont.slideMenu.Tournament }}
                    </span>
                </template>
                <!-- <el-menu-item index="/tournament/list">
                    <i class="el-icon-thirdlist"></i>
                    <span slot="title">
                        {{ $store.state.langData.cont.slideMenu.List }}
                    </span>
                </el-menu-item> -->
                <el-menu-item @click.native.prevent="tournamentLive">
                    <i class="el-icon-thirdlive"></i>
                    <span slot="title">
                        {{ $store.state.langData.cont.slideMenu.Live }}
                    </span>
                </el-menu-item>
            </el-submenu>
            <el-submenu index="3">
                <template slot="title">
                    <i class="el-icon-thirdleague"></i>
                    <span slot="title">
                        {{ $store.state.langData.cont.slideMenu.League }}
                    </span>
                </template>
                <el-menu-item @click.native.prevent="leagueLive">
                    <i class="el-icon-thirdlive"></i>
                    <span slot="title">
                        {{ $store.state.langData.cont.slideMenu.Live }}
                    </span>
                </el-menu-item>
            </el-submenu>
            <el-menu-item index="4" @click.native.prevent="logout">
                <i class="el-icon-thirdlogout"></i>
                <span slot="title">
                    {{ $store.state.langData.cont.slideMenu.Logout }}
                </span>
            </el-menu-item>
        </el-menu>
    </div>
</template>
<script>
import { mapActions } from 'vuex'
export default {
    data() {
        return {
            isCollapse: false
        }
    },
    computed: {},
    methods: {
        ...mapActions(['changeDrawerStatus', 'setMe', 'changeLoginStatus']),
        leagueLive() {
            window.location.href = `/league/live`
        },
        tournamentLive() {
            window.location.href = `/tournament/live`
        },
        logout() {
            this.changeLoginStatus(true)
            localStorage.removeItem('id')
            this.setMe = {}
            this.$router.push({ path: '/login' })
        }
    }
}

</script>
<style scoped lang="scss">
</style>
