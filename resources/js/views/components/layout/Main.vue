<template>
    <el-container class="main">
        <el-main>
            <el-row class="hidden-md-and-up" type="flex" justify="start" style="margin-bottom: .5rem">
                <el-col>
                    <i class="iconfont el-icon-thirdbars bars" @click="changeDrawerStatus(true)"></i>
                </el-col>
            </el-row>
            <router-view></router-view>
        </el-main>
    </el-container>
</template>
<script>
import { mapActions } from 'vuex'
export default {
    data() {
        return {}
    },
    methods: {
        ...mapActions(['changeDrawerStatus']),
        logout() {

        }
    }
}

</script>
