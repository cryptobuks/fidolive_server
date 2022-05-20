<template>
    <div class="equipment-port">
        <Breadcrumb :iteams="br" />
        <br>
        <h3 style="text-align: center;">{{ eName }}</h3>
        <p style="text-align: center;">
            <a :href="viewUrl" target="_blank">
                {{ $store.state.langData.cont.pageFn.table.View }}
            </a>
            <span style="margin: 0 .75rem">|</span>
            <el-button @click.native.prevent="handleCopyUrl" type="text">
                {{ $store.state.langData.cont.pageFn.table.CopyUrl }}
            </el-button>
            <span style="margin: 0 .75rem">|</span>
            <el-button @click.native.prevent="handleSearchWebcam" type="text">
                {{ $store.state.langData.cont.pageFn.table['Search Webcam'] }}
            </el-button>
        </p>
        <br>
        <el-table :data="tableData" border :empty-text="$store.state.langData.cont.msg.data.d0001" style="width: 100%">
            <el-table-column prop="usb_id" :label="$store.state.langData.cont.pageFn.table.USB">
            </el-table-column>
            <el-table-column :label="$store.state.langData.cont.pageFn.table.Type">
                <template slot-scope="scope">
                    {{ $store.state.langData.cont.pageFn.equipment.portFunction[scope.row.usb_id - 1] }}
                </template>
            </el-table-column>
            <el-table-column prop="dev_name" :label="$store.state.langData.cont.pageFn.table.Name">
            </el-table-column>
            <el-table-column prop="port_no" :label="$store.state.langData.cont.pageFn.table.Port">
            </el-table-column>
            <el-table-column fixed="right" :label="$store.state.langData.cont.pageFn.table.Operating" width="100">
                <template slot-scope="scope">
                    <p v-if="scope.row.usb_id===1" style="margin: 0;">
                        <el-button @click.native.prevent="handleAudio(scope.$index, scope.row)" type="text">
                            {{ scope.row.audio? $store.state.langData.cont.pageFn.table.Mute : $store.state.langData.cont.pageFn.table.Audio }}
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
    props: ['id'],
    sockets: {
        uvcvideoDone() {
            setTimeout(() => {
                window.location.reload()
            }, 3000)
        }
    },
    data() {
        return {
            br: [{
                    hasback: true,
                    name: 'EquipmentList',
                    isUrl: false
                },
                {
                    title: this.$store.state.langData.cont.slideMenu.Equipment,
                    isUrl: false
                }, {
                    title: this.$store.state.langData.cont.pageFn.golbal.Port,
                    isUrl: false
                }
            ],
            tableData: [],
            e_name: ''
        }
    },
    created() {
        this.changeAppLoadingStatus(true)
        this.fetchData()
    },
    computed: {
        viewUrl() {
            let id = Base64.encode('fidodartsVideoLive-' + this.id)
            return `http://${document.location.hostname}/view/eg?id=${id}`
        },
        eName() {
            return this.e_name
        }
    },
    methods: {
        ...mapActions(['changeAppLoadingStatus']),
        fetchData() {
            axios
                .get('/api/getEquipmentPort', {
                    params: { id: this.id }
                })
                .then(response => {
                    let data = response.data.data
                    console.log(data)
                    if (data.errorCode === 'er0000') {
                        this.tableData = data.data
                        this.e_name = this.tableData[0].ename
                        this.changeAppLoadingStatus(false)
                    }
                }).catch(error => {
                    this.changeAppLoadingStatus(false)
                    console.log(error)
                })
        },
        handleAudio(index, row) {
            this.changeAppLoadingStatus(true)
            axios
                .post('/api/setAudio', {
                    audio: row.audio,
                    id: this.id
                })
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
                    this.$message({
                        message: JSON.stringify(error),
                        type: 'error',
                        offset: 90
                    })
                    this.changeAppLoadingStatus(false)
                })
        },
        handleCopyUrl() {
            const el = document.createElement('textarea')
            el.value = this.viewUrl
            el.setAttribute('readonly', '')
            el.style.position = 'absolute'
            el.style.left = '-9999px'
            document.body.appendChild(el)
            const selected = document.getSelection().rangeCount > 0 ? document.getSelection().getRangeAt(0) : false
            el.select()
            try {
                var successful = document.execCommand('copy')
                this.$message({
                    message: this.$store.state.langData.cont.msg.golbal.g0001,
                    type: 'success',
                    offset: 90
                })
                document.body.removeChild(el)
            } catch (err) {
                this.$message({
                    message: this.$store.state.langData.cont.msg.golbal.g0002,
                    type: 'error',
                    offset: 90
                })
            }
            if (selected) {
                document.getSelection().removeAllRanges()
                document.getSelection().addRange(selected)
            }
        },
        handleSearchWebcam() {
            this.changeAppLoadingStatus(true)
            this.$socket.client.emit('uvcvideoSearch', { id: this.id })
        }
    }
}

</script>
<style scoped lang="scss">
a {
    text-decoration: none;
    color: #409EFF;
}

</style>
