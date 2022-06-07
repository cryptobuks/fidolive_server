<template>
    <div class="equipment-list">
        <Breadcrumb :iteams="br" />
        <el-row type="flex" justify="space-between" style="margin-bottom: 15px">
            <el-col :xs="2" :sm="3" :span="8">
                <el-button type="success" icon="el-icon-refresh" circle @click.native.prevent="handleRefresh"></el-button>
            </el-col>
            <el-col v-if="$store.state.gobalData.me.roleCode === 1" :xs="4" :sm="5" :span="8" style="text-align: right;">
                <el-button class="hidden-sm-and-down" type="success" round @click="addEquipment">{{ $store.state.langData.cont.pageFn.equipment.addBtn }}</el-button>
                <el-button class="hidden-md-and-up" type="success" icon="el-icon-plus" circle @click="addEquipment"></el-button>
            </el-col>
        </el-row>
        <el-table :data="tableData" border :empty-text="$store.state.langData.cont.msg.data.d0001" style="width: 100%">
            <el-table-column type="index" fixed>
            </el-table-column>
            <el-table-column prop="s_name" fixed :label="$store.state.langData.cont.pageFn.table.Store">
            </el-table-column>
            <el-table-column prop="no" :label="$store.state.langData.cont.pageFn.table.SN">
            </el-table-column>
            <el-table-column prop="name" :label="$store.state.langData.cont.pageFn.table.Name" min-width="100">
            </el-table-column>
            <el-table-column prop="d_name" :label="$store.state.langData.cont.pageFn.table.Distributor" v-if="$store.state.gobalData.me.roleCode === 1">
            </el-table-column>
            <el-table-column prop="machine_name" :label="$store.state.langData.cont.pageFn.table.Machine" v-if="$store.state.gobalData.me.roleCode === 1">
            </el-table-column>
            <el-table-column prop="mac" :label="$store.state.langData.cont.pageFn.table.MAC" v-if="$store.state.gobalData.me.roleCode === 1">
            </el-table-column>
            <el-table-column :label="$store.state.langData.cont.pageFn.table.IP">
                <template slot-scope="scope">
                    <el-link v-if="scope.row.status" type="success" @click.native.prevent="handlePi(scope.$index, scope.row)">{{ scope.row.ip }}</el-link>
                    <p>
                        <el-link v-if="scope.row.status" type="success" @click.native.prevent="handlePreview(scope.$index, scope.row)">{{ $store.state.langData.cont.pageFn.table.preview }}</el-link>
                    </p>
                    <p>
                        <el-link v-if="scope.row.status" type="success" @click.native.prevent="handleSetDartBoard(scope.$index, scope.row)">{{ $store.state.langData.cont.pageFn.table.setDartBoard }}</el-link>
                    </p>
                    <p>version : {{ scope.row.version }}</p>
                </template>
            </el-table-column>
            <el-table-column :label="$store.state.langData.cont.pageFn.table.Operating" width="100">
                <template slot-scope="scope">
                    <p style="margin: 0;">
                        <el-button @click.native.prevent="handleEdit(scope.$index, scope.row)" type="text">
                            {{ $store.state.langData.cont.pageFn.table.Edit }}
                        </el-button>
                    </p>
                    <p style="margin: 0;">
                        <el-button @click.native.prevent="handleDelete(scope.$index, scope.row)" type="text">
                            {{ $store.state.langData.cont.pageFn.table.Delete }}
                        </el-button>
                    </p>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>
<script>
import { mapActions } from 'vuex'
import Breadcrumb from '../layout/Breadcrumb.vue'
export default {
    components: { Breadcrumb },
    sockets: {
        connect() {
            console.log('connect')
            setTimeout(() => {
                this.fetchData()
            },3000)
        },
        changeConnectEquipment() {
            console.log('changeConnectEquipment')
            setTimeout(() => {
                this.fetchData()
            },3000)
        },
        disconnect() {
            console.log('disconnect')
            this.clearPiContent()
        }
    },
    data() {
        return {
            br: [{
                title: this.$store.state.langData.cont.slideMenu.Equipment,
                isUrl: false
            }, {
                title: this.$store.state.langData.cont.slideMenu.List,
                isUrl: false
            }],
            tableData: [],
            awsUrl: 'http://fidolive.ga'
        }
    },
    created() {
        var route = this.$route
        this.changeTitle({ title: 'default', name: route.name })
        this.changeAppLoadingStatus(true)
    },
    mounted() {
        setTimeout(() => {
            if (!this.$socket.connected) {
                this.clearPiContent()
            }
        }, 3000)
        this.fetchData()
    },
    methods: {
        ...mapActions(['changeAppLoadingStatus', 'changeLoadingText', 'changeTitle']),
        fetchData() {
            axios
                .get('/api/getEquipmentListData', {
                    params: { distributor_id: this.$store.state.gobalData.me.roleID }
                })
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        this.tableData = data.data
                        this.changeAppLoadingStatus(false)
                    }
                }).catch(error => {
                    this.changeAppLoadingStatus(false)
                    console.log(error)
                })
        },
        addEquipment() {
            this.$router.push({ name: 'AddEquipment' })
        },
        handleRefresh() {
            this.changeAppLoadingStatus(true)
            this.fetchData()
        },
        handlePi(index, row) {
            axios
                .get('/api/getEquipmentPort', { params: { id: row.id } })
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        var port = data.data[1].port_no
                        window.open(`${this.awsUrl}:${port}`, '_blank')
                    } else {}
                }).catch(error => {})
        },
        handlePreview(index, row) {
            let routeData = this.$router.resolve({ name: 'Pv', query: { id: row.id, type: 'combination' } })
            window.open(routeData.href, '_blank')
        },
        handleSetDartBoard(index, row) {
            let routeData = this.$router.resolve({ name: 'Pv', query: { id: row.id, type: 'dartboard' } })
            window.open(routeData.href, '_blank')
        },
        handleEdit(index, row) {
            this.$router.push({ name: 'EditEquipment', query: { id: row.id } })
        },
        handleDelete(index, row) {
            this.changeAppLoadingStatus(true)
            axios
                .delete('/api/deleteEquipment', { params: { id: row.id } })
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        this.$message({
                            message: this.$store.state.langData.cont.msg.database.ok0003,
                            type: 'success',
                            offset: 90
                        })
                        this.fetchData()
                    } else {
                        this.$message({
                            message: this.$store.state.langData.cont.msg.database[data.errorCode],
                            type: 'error',
                            offset: 90
                        })
                        this.changeAppLoadingStatus(false)
                    }
                }).catch(error => {
                    this.changeAppLoadingStatus(false)
                    this.$message({
                        message: JSON.stringify(error),
                        type: 'error',
                        offset: 90
                    })
                })
        },
        clearPiContent() {
            axios
                .post('/api/disconentEquipmentStatus')
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        this.fetchData()
                    } else {}
                }).catch(error => {})
        }
    }
}

</script>
