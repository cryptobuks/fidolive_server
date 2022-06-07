<template>
    <div class="no-user-select text-center">
        <h1>{{ ename }}</h1>
        <el-row>
            <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                <iframe frameborder="0" scrolling="no" allow="autoplay" :src="src"></iframe>
            </el-col>
            <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                <el-table :data="tableData" style="width: 100%">
                    <el-table-column prop="no" label="No" width="80">
                    </el-table-column>
                    <el-table-column :label="$store.state.langData.cont.pageFn.table.Zoom">
                        <template slot-scope="scope">
                            <span v-if="!scope.row.edit">{{ scope.row.zoom }}</span>
                            <el-input-number v-if="scope.row.edit" v-model="scope.row.zoom" @change="handleZoomChange" :min="1" :max="5" :step="0.1" size="mini"></el-input-number>
                        </template>
                    </el-table-column>
                    <el-table-column prop="up" :label="$store.state.langData.cont.pageFn.table.Up">
                    </el-table-column>
                    <el-table-column prop="left" :label="$store.state.langData.cont.pageFn.table.Left">
                    </el-table-column>
                    <el-table-column :label="$store.state.langData.cont.pageFn.table.Operating" width="80">
                        <template slot-scope="scope">
                            <el-button v-if="!scope.row.edit" @click.native.prevent="handleEdit(scope.$index, scope.row)" type="text">
                                {{ $store.state.langData.cont.pageFn.table.Edit }}
                            </el-button>
                            <el-button v-if="scope.row.edit" @click.native.prevent="handleOk(scope.$index, scope.row)" type="text">
                                {{ $store.state.langData.cont.pageFn.golbal.Save }}
                            </el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </el-col>
        </el-row>
    </div>
</template>
<script>
import { mapActions } from 'vuex'
export default {
    components: {},
    props: ['id'],
    data() {
        return {
            type: 'dartboard',
            ports: [],
            awsUrl: 'http://fidolive.ga',
            name: null,
            tableData: [{
                no: 20,
                zoom: 1,
                up: 0,
                left: 0,
                edit: false
            }, {
                no: 1,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 18,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 4,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 13,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 6,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 10,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 15,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 2,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 17,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 3,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 19,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 7,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 16,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 8,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 14,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 9,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 12,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 5,
                zoom: 1,
                up: 0,
                left: 0
            }]
        }
    },
    created() {
        var route = this.$route
        this.changeTitle({ title: 'default', name: route.name })
        this.fetchData()
    },
    computed: {
        src() {
            var url = ''
            var p_80 = this.ports.find(item => {
                return item.port_name === 80
            })
            var p_8000 = this.ports.find(item => {
                return item.port_name === 8000
            })
            //console.log(p_80)
            if (p_80 && p_8000) {
                url = `${this.awsUrl}:${p_80.port_no}/${this.type}?p_8000=${p_8000.port_no}`
            }
            return url
        },
        ename() {
            var n = ' '
            if (this.name !== null) {
                n += this.name.no
                n += ' '
                n += this.name.name
                if (this.name.machine_id) {
                    n += ' / '
                    n += this.name.machine_id
                }
                if (this.name.machine_name) {
                    n += ' '
                    n += this.name.machine_name
                }
            }
            return n
        }
    },
    methods: {
        ...mapActions(['changeTitle']),
        fetchData() {
            this.getName()
            this.getPort()
        },
        getPort() {
            axios
                .get('/api/getEquipmentPort', {
                    params: { id: this.id }
                })
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        this.ports = data.data
                    }
                }).catch(error => {
                    console.log(error)
                })
        },
        getName() {
            axios
                .get('/api/getEquipmentName', {
                    params: { id: this.id }
                })
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        console.log(data.data[0])
                        this.name = data.data[0]
                    }
                }).catch(error => {
                    console.log(error)
                })
        },
        handleEdit(index, row) {
            row.edit = true
        },
        handleOk(index, row) {

        },
        handleZoomChange(value) {
            console.log(value)
        }
    }
}

</script>
<style scoped lang="scss">
iframe {
    width: 100%;
    height: calc(792/960*100vw);
}

.el-table {
    .el-table__cell {
        text-align: center !important;
    }
}

</style>
