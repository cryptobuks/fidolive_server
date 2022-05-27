<template>
    <div class="equipment-add">
        <Breadcrumb :iteams="br" />
        <br>
        <el-form :model="form" :rules="rules" ref="ruleForm" label-position="top">
            <el-form-item :label="$store.state.langData.cont.pageFn.table.Distributor" prop="distributorId">
                <el-select v-model="form.distributorId" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" clearable filterable @change="getStore" @clear="clear" :no-data-text="$store.state.langData.cont.msg.data.d0001" style="width: 100%">
                    <el-option v-for="item in distributors" :key="item.id" :label="item.value" :value="item.id">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item :label="$store.state.langData.cont.pageFn.table.Store" prop="storeId">
                <el-select v-model="form.storeId" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" clearable filterable :disabled="stores.length===0" @change="getMachine" @clear="clear" :no-data-text="$store.state.langData.cont.msg.data.d0001" :no-match-text="$store.state.langData.cont.msg.data.d0002" style="width: 100%">
                    <el-option v-for="item in stores" :key="item.id" :label="item.value" :value="item.id">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item :label="$store.state.langData.cont.pageFn.table.Machine" prop="machineId">
                <el-select v-model="form.machineId" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" clearable filterable :disabled="machines.length===0" :no-data-text="$store.state.langData.cont.msg.data.d0001" :no-match-text="$store.state.langData.cont.msg.data.d0002" style="width: 100%">
                    <el-option v-for="item in machines" :key="item.id" :label="item.value" :value="item.id" :disabled="useMachines.includes(item.id)">
                        <span>{{ item.value }}</span>
                        <span v-if="useMachines.includes(item.id)">
                            ( {{ $store.state.langData.cont.pageFn.equipment.useMachine }} )
                        </span>
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item :label="$store.state.langData.cont.pageFn.table.MAC" prop="mac">
                <el-input v-model="form.mac"></el-input>
            </el-form-item>
            <el-form-item :label="$store.state.langData.cont.pageFn.table.Name" prop="name">
                <el-input v-model="form.name"></el-input>
            </el-form-item>
            <el-form-item :label="$store.state.langData.cont.pageFn.table.Description" prop="description">
                <el-input type="textarea" :rows="6" v-model="form.description"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click.native.prevent="add" :loading="loading">
                    {{ $store.state.langData.cont.pageFn.golbal.Add }}
                </el-button>
                <el-button @click.native.prevent="cancel">
                    {{ $store.state.langData.cont.pageFn.golbal.Cancel }}
                </el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import Breadcrumb from '../layout/Breadcrumb.vue'
export default {
    components: { Breadcrumb },
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
                    title: this.$store.state.langData.cont.pageFn.golbal.Add,
                    isUrl: false
                }
            ],
            distributors: [],
            stores: [],
            machines: [],
            useMachines: [],
            form: {
                mac: '',
                name: '',
                distributorId: '',
                storeId: '',
                machineId: '',
                machineName: '',
                description: `Raspberry Pi 4 Model B 8G\nSD:32G\nLinux raspbian 5.10.78-Release-OPENFANS+20211111-v8 #1 SMP PREEMPT Thu Nov 11 15:43:52 CST 2021 aarch64 GNU/Linux)`,
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
        this.getDistributorData()
    },
    methods: {
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
        getStore() {
            this.form.storeId = ''
            this.form.machineId = ''
            this.form.machineName = ''
            this.stores = []
            this.machines = []
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
                        }
                    }).catch(error => {
                        this.stores = []
                        console.log(error)
                    })
            }
        },
        getMachine() {
            this.form.machineId = ''
            this.form.machineName = ''
            this.machines = []
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
                        }
                    }).catch(error => {
                        this.machines = []
                        console.log(error)
                    })
            }
        },
        add() {
            this.$refs['ruleForm'].validate((valid) => {
                if (valid) {
                    if (this.form.machineId === -1) {
                        this.form.machineId = ''
                    }
                    this.loading = true
                    axios
                        .post('/api/addEquipment', this.form)
                        .then(response => {
                            let data = response.data.data
                            if (data.errorCode === 'er0000') {
                                this.$message({
                                    message: this.$store.state.langData.cont.msg.database.ok0001,
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
        }
    },
    watch: {}
}

</script>
