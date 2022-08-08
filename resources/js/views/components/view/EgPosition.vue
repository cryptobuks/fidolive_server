<template>
    <div class="no-user-select text-center">
        <h1>{{ ename }}</h1>
        <el-row>
            <el-col :xs="24" :sm="24" :md="24" :lg="12" :xl="12" style="margin-bottom: 1.25rem;">
                <iframe frameborder="0" scrolling="no" allow="autoplay" :src="src" ref="iframe"></iframe>
            </el-col>
            <el-col v-if="showSetTable" :xs="24" :sm="24" :md="24" :lg="12" :xl="12">
                <table style="width: 96%; margin-left: 2%;">
                    <tr>
                        <th>No</th>
                        <td>
                            <el-select v-model="value" @change="handleSelectNo" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" style="width: 100%; margin-bottom: .75rem;">
                                <el-option v-for="(item, index) in tableData" :key="item.no" :label="item.no" :value="index">
                                </el-option>
                            </el-select>
                        </td>
                    </tr>
                    <tr v-if="value!==null">
                        <th>{{ $store.state.langData.cont.pageFn.table.Zoom }}</th>
                        <td>
                            <el-input-number v-model="tableData[value].zoom" @change="handleZoomChange" :min="1" :max="1.5" :step="0.05" style="width: 100%; margin-bottom: .75rem;"></el-input-number>
                        </td>
                    </tr>
                    <tr v-if="value!==null">
                        <th>{{ $store.state.langData.cont.pageFn.table.Up }}</th>
                        <td>
                            <el-input-number v-model="tableData[value].up" @change="handleUpChange" :min="-200" :max="200" :step="5" style="width: 100%; margin-bottom: .75rem;"></el-input-number>
                        </td>
                    </tr>
                    <tr v-if="value!==null">
                        <th>{{ $store.state.langData.cont.pageFn.table.Left }}</th>
                        <td>
                            <el-input-number v-model="tableData[value].left" @change="handleLeftChange" :min="-300" :max="300" :step="5" style="width: 100%; margin-bottom: .75rem;"></el-input-number>
                        </td>
                    </tr>
                </table>
                <el-row v-if="value!==null">
                    <el-button type="primary" @click.native.prevent="save" :loading="loading">
                        {{ $store.state.langData.cont.pageFn.golbal.Save }}
                    </el-button>
                </el-row>
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
            value: null,
            tableData: [{
                no: 20,
                zoom: 1,
                up: 0,
                left: 0
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
            }],
            iframeWin: {},
            showSetTable: false,
            loading: false
        }
    },
    created() {
        var route = this.$route
        this.changeTitle({ title: 'default', name: route.name })
        this.fetchData()
    },
    mounted() {
        window.addEventListener('message', (event) => {
            const data = event.data
            if (data.cmd === 'echo') {
                this.handleEcho(data)
            }
        })
        this.iframeWin = this.$refs.iframe.contentWindow
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
            var n = ''
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
                        this.name = data.data[0]
                    }
                }).catch(error => {
                    console.log(error)
                })
        },
        handleSelectNo() {
            this.iframeWin.postMessage({
                cmd: 'run',
                params: { fn: 'position', value: this.tableData[this.value] }
            }, '*')
        },
        handleZoomChange(value) {
            this.iframeWin.postMessage({
                cmd: 'set',
                params: { fn: 'zoom', value: value }
            }, '*')
        },
        handleUpChange(value) {
            this.iframeWin.postMessage({
                cmd: 'set',
                params: { fn: 'up', value: value }
            }, '*')
        },
        handleLeftChange(value) {
            this.iframeWin.postMessage({
                cmd: 'set',
                params: { fn: 'left', value: value }
            }, '*')
        },
        save() {
            this.loading = true
            var data = this.tableData[this.value]
            this.iframeWin.postMessage({
                cmd: 'set',
                params: { fn: 'db', data: data }
            }, '*')
        },
        handleEcho(data) {
            switch (data.fn) {
                case 'play':
                    this.showSetTable = true
                    break
                case 'position':
                    this.loading = false
                    if (data.data.status) {
                        this.$message({
                            message: this.$store.state.langData.cont.msg.database.ok0004,
                            type: 'success',
                            offset: 90
                        })
                    } else {
                        this.$message({
                            message: this.$store.state.langData.cont.msg.database[data.data.errorCode],
                            type: 'error',
                            offset: 90
                        })
                    }
                    break
                case 'positionData':
                    this.tableData.map(item => {
                        var d = data.data.pt.filter(pt => {
                            return pt.no === item.no
                        })
                        if (d.length === 0) {
                            d = data.data.dpt.filter(pt => {
                                return pt.no === item.no
                            })
                        }
                        item.zoom = d[0].zoom
                        item.up = d[0].up
                        item.left = d[0].l
                    })
                    break
            }
        }
    }
}

</script>
<style scoped lang="scss">
iframe {
    width: 100%;
    height: calc(792/1920*100vw);
}

@media (min-width: 40px) and (max-width: 575.98px) {
    iframe {
        height: calc(792/960*100vw);
    }
}

</style>
<style scoped>
>>>.el-table .el-table__cell {
    text-align: center !important;
}

</style>
