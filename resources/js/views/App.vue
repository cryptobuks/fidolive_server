<template>
    <div>
        <el-container v-if="!isGuest && !isView" v-loading="appLoading" :element-loading-text="loadingText" element-loading-spinner="el-icon-loading">
            <!-- 側邊欄 -->
            <div class="hidden-sm-and-down">
                <Aside />
            </div>
            <!-- 右側內容主體 -->
            <Main />
            <!-- left menu for phone -->
            <el-drawer title="" :visible="drawer" :direction="direction" :size="'100%'" custom-class="hidden-md-and-up" @close="changeDrawerStatus(false)">
                <!-- 側邊欄 -->
                <Aside />
            </el-drawer>
        </el-container>
        <router-view v-else></router-view>
    </div>
</template>
<script>
import { mapState, mapGetters, mapActions } from 'vuex'
import Aside from './components/layout/Aside.vue'
import Main from './components/layout/Main.vue'
export default {
    components: { Aside, Main },
    props: ['text', 'ev'],
    data() {
        return {
            direction: 'ltr'
        }
    },
    created() {
        this.setLangCont(this.text)
        this.setEV(this.ev)
        console.log(this.$store.state.gobalData.ev)
        var path = this.$route.path
        var paths = path.split('/')
        if (paths[1] === 'view') {
            this.changePageStatus(true)
        }
        if (localStorage.getItem('id') !== null) {
            var id = Base64.decode(localStorage.getItem('id'))
            var ids = id.split(',')
            this.setMe({
                id: ids[0],
                roleID: ids[1],
                roleCode: ids[2] * 1
            })
            this.changeLoginStatus(false)
        }
    },
    computed: mapState({
        isGuest: state => state.gobalData.isGuest,
        appLoading: state => state.gobalData.appLoading,
        loadingText: state => state.gobalData.loadingText,
        drawer: state => state.gobalData.drawer,
        isView: state => state.gobalData.isView,
    }),
    mounted() {},
    methods: {
        ...mapActions(['setLangCont', 'changeLoginStatus', 'changeDrawerStatus', 'setMe', 'changePageStatus', 'setEV'])
    },
    watch: {}
}

</script>
<style scoped lang="scss">
</style>
