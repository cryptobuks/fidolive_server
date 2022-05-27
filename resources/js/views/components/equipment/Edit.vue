<template>
    <div class="equipment-add">
        <Breadcrumb :iteams="br" />
        <br>
        <el-form :model="form" :rules="rules" ref="ruleForm" label-position="top">
            <el-form-item v-if="$store.state.gobalData.me.roleCode === 1" :label="$store.state.langData.cont.pageFn.table.Distributor" prop="distributorId">
                <el-select v-model="form.distributorId" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" clearable filterable @change="getStore(true)" @clear="clear" :no-data-text="$store.state.langData.cont.msg.data.d0001" style="width: 100%">
                    <el-option v-for="item in distributors" :key="item.id" :label="item.value" :value="item.id">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item v-if="$store.state.gobalData.me.roleCode === 1" :label="$store.state.langData.cont.pageFn.table.Store" prop="storeId">
                <el-select v-model="form.storeId" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" clearable filterable :disabled="stores.length===0" @change="getMachine(true)" @clear="clear" :no-data-text="$store.state.langData.cont.msg.data.d0001" :no-match-text="$store.state.langData.cont.msg.data.d0002" style="width: 100%">
                    <el-option v-for="item in stores" :key="item.id" :label="item.value" :value="item.id">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item v-if="$store.state.gobalData.me.roleCode === 1" :label="$store.state.langData.cont.pageFn.table.Machine" prop="machineId">
                <el-select v-model="form.machineId" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" clearable filterable :disabled="machines.length===0" @clear="clear" :no-data-text="$store.state.langData.cont.msg.data.d0001" :no-match-text="$store.state.langData.cont.msg.data.d0002" style="width: 100%">
                    <el-option v-for="item in machines" :key="item.id" :label="item.value" :value="item.id" :disabled="originalForm.machineId!==item.id && useMachines.includes(item.id)">
                        <span>{{ item.value }}</span>
                        <span v-if="originalForm.machineId===item.id">
                            ( {{ $store.state.langData.cont.pageFn.equipment.curentMachine }} )
                        </span>
                        <span v-else-if="useMachines.includes(item.id)">
                            ( {{ $store.state.langData.cont.pageFn.equipment.useMachine }} )
                        </span>
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item v-if="$store.state.gobalData.me.roleCode === 1" :label="$store.state.langData.cont.pageFn.table.MAC" prop="mac">
                <el-input v-model="form.mac"></el-input>
            </el-form-item>
            <el-form-item :label="$store.state.langData.cont.pageFn.table.Name" prop="name">
                <el-input v-model="form.name"></el-input>
            </el-form-item>
            <el-form-item v-if="$store.state.gobalData.me.roleCode === 1" :label="$store.state.langData.cont.pageFn.table.Description" prop="description">
                <el-input type="textarea" :rows="6" v-model="form.description"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click.native.prevent="save" :loading="loading">
                    {{ $store.state.langData.cont.pageFn.golbal.Save }}
                </el-button>
                <el-button type="info" @click.native.prevent="reset">
                    {{ $store.state.langData.cont.pageFn.golbal.Reset }}
                </el-button>
                <el-button @click.native.prevent="cancel">
                    {{ $store.state.langData.cont.pageFn.golbal.Cancel }}
                </el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<script>
import { mapActions } from 'vuex'
import Breadcrumb from '../layout/Breadcrumb.vue'
export default {
    components: { Breadcrumb },
    props: ['id'],
    data() {
        var validateMac = (rule, value, callback) => {
            if (value.trim() === '') {
                callback(new Error(this.$store.state.langData.cont.msg.validate.er0051))
            } else {
                var regexp = /^(([A-Fa-f0-9]{2}[:]){5}[A-Fa-f0-9]{2}[,]?)+$/i
                if (regexp.test(value)) {
                    callback()
                } else {
                    callback(new Error(this.$store.state.langData.cont.msg.validate.er0101))
                }
            }
        }
        var validateName = (rule, value, callback) => {
            if (value.trim() === '') {
                callback(new Error(this.$store.state.langData.cont.msg.validate.er0050))
            } else {
                callback()
            }
        }
        var validateDistributor = (rule, value, callback) => {
            if (value.trim() === '') {
                callback(new Error(this.$store.state.langData.cont.msg.validate.er0053))
            } else {
                callback()
            }
        }
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
                    title: this.$store.state.langData.cont.pageFn.golbal.Edit,
                    isUrl: false
                }
            ],
            distributors: [],
            stores: [],
            machines: [],
            useMachines: [],
            form: {
                id: '',
                mac: '',
                name: '',
                distributorId: '',
                storeId: '',
                machineId: '',
                machineName: '',
                description: ''
            },
            originalForm: {},
            original: {
                stores: [],
                machines: []
            },
            loading: false,
            rules: {
                mac: [
                    { validator: validateMac, required: true, trigger: 'no' }
                ],
                name: [
                    { validator: validateName, required: true, trigger: 'no' }
                ],
                distributorId: [
                    { validator: validateDistributor, required: true, trigger: 'no' }
                ]
            }
        }
    },
    created() {
        this.changeAppLoadingStatus(true)
        this.fetchData()
    },
    methods: {
        ...mapActions(['changeAppLoadingStatus']),
        fetchData() {
            axios
                .get('/api/getEquipment', {
                    params: { id: this.id }
                })
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        this.originalForm = Object.assign({}, data.data)
                        //console.log(data)
                        this.getDistributorData()
                        this.form.id = data.data.id
                        this.form.mac = data.data.mac
                        this.form.name = data.data.name
                        this.form.machineName = data.data.machineName
                        this.form.description = data.data.description
                    }
                }).catch(error => {
                    this.changeAppLoadingStatus(false)
                    console.log(error)
                })
        },
        getDistributorData() {
            axios
                .get('/api/getDistributorData', {})
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        data.data.map(iteam => {
                            var opt = {}
                            opt.value = iteam.value
                            opt.id = iteam.id
                            this.distributors.push(opt)
                        })
                        this.form.distributorId = this.originalForm.distributorId
                        this.getStore(false, true)
                    }
                }).catch(error => {
                    console.log(error)
                })
        },
        clear() {
            if (this.form.distributorId === '') {
                this.form.storeId = ''
                this.form.machineId = ''
                this.form.machineName = ''
                this.stores = []
                this.machines = []
            } else if (this.form.storeId === '') {
                this.form.machineId = ''
                this.form.machineName = ''
                this.machines = []
            }
        },
        getStore(clear, init = false) {
            if (clear) {
                this.form.storeId = ''
                this.form.machineId = ''
                this.form.machineName = ''
                this.stores = []
                this.machines = []
            }
            if (this.form.distributorId !== '') {
                axios
                    .get('/api/getStoreData', {
                        params: { distributorId: this.form.distributorId }
                    })
                    .then(response => {
                        let data = response.data.data
                        if (data.errorCode === 'er0000') {
                            data.data.map(iteam => {
                                var opt = {}
                                opt.value = iteam.value
                                opt.id = iteam.id
                                opt.distributorId = iteam.distributorId
                                this.stores.push(opt)
                            })
                            if (init) {
                                this.form.storeId = this.originalForm.storeId
                                this.original.stores = this.stores
                                if (this.form.storeId !== '') {
                                    this.getMachine(false, true)
                                } else {
                                    this.changeAppLoadingStatus(false)
                                }
                            }
                        }
                    }).catch(error => {
                        this.stores = []
                        console.log(error)
                    })
            }
        },
        getMachine(clear, init = false) {
            if (clear) {
                this.form.machineId = ''
                this.form.machineName = ''
                this.machines = []
            }
            if (this.form.storeId !== '') {
                axios
                    .get('/api/getMachineData', {
                        params: { storeId: this.form.storeId }
                    })
                    .then(response => {
                        let data = response.data.data
                        if (data.errorCode === 'er0000') {
                            data.data.map(iteam => {
                                var opt = {}
                                opt.value = iteam.value
                                opt.id = iteam.id
                                opt.storeId = iteam.storeId
                                opt.distributorId = iteam.distributorId
                                this.machines.push(opt)
                            })
                            this.useMachines = data.use
                            if (init) {
                                this.original.machines = this.machines
                                this.form.machineId = this.originalForm.machineId
                                this.changeAppLoadingStatus(false)
                            }
                        }
                    }).catch(error => {
                        this.machines = []
                        console.log(error)
                    })
            }

        },
        reset() {
            this.stores = this.original.stores
            this.machines = this.original.machines
            for (const key in this.originalForm) {
                this.form[key] = this.originalForm[key]
            }
        },
        save() {
            this.$refs['ruleForm'].validate((valid) => {
                if (valid) {
                    if (this.form.machineId === null || this.form.machineId === '') {
                        this.form.machineName = ''
                    } else {
                        var machine = this.machines.find(item => {
                            return item.id === this.form.machineId
                        })
                        this.form.machineName = machine.value
                    }
                    this.loading = true
                    axios
                        .post('/api/updateEquipment', this.form)
                        .then(response => {
                            let data = response.data.data
                            if (data.errorCode === 'er0000') {
                                this.$message({
                                    message: this.$store.state.langData.cont.msg.database.ok0002,
                                    type: 'success',
                                    offset: 90
                                })
                                this.$router.replace({ name: 'EquipmentList' })
                            } else {
                                this.$message({
                                    message: this.$store.state.langData.cont.msg.database[data.errorCode],
                                    type: 'error',
                                    offset: 90
                                })
                            }
                        }).catch(error => {
                            this.loading = false
                            this.$message({
                                message: JSON.stringify(error),
                                type: 'error',
                                offset: 90
                            })
                        })
                } else {
                    return false
                }
            })
        },
        cancel() {
            this.$router.replace({ name: 'EquipmentList' })
        },
    },
    watch: {}
}

</script>
