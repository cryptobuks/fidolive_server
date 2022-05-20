<template>
    <div class="equipment-add">
        <Breadcrumb :iteams="br" />
        <br>
        <el-form :model="form" :rules="rules" ref="ruleForm" label-position="top">
            <el-form-item :label="$store.state.langData.cont.pageFn.table.Distributor" prop="distributor">
                <el-autocomplete ref="distributor" v-model="form.distributor" clearable :fetch-suggestions="querySearchAsync" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" @select="handleSelect" @blur="handleBlur" @clear="handleClear" style="width: 100%"></el-autocomplete>
            </el-form-item>
            <el-form-item :label="$store.state.langData.cont.pageFn.table.Store" prop="store">
                <el-autocomplete ref="store" v-model="form.store" clearable :fetch-suggestions="querySearchStoreAsync" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" @select="handleStoreSelect" @blur="handleStoreBlur" @clear="handleStoreClear" style="width: 100%"></el-autocomplete>
            </el-form-item>
            <el-form-item :label="$store.state.langData.cont.pageFn.table.Machine" prop="machineId">
                <el-autocomplete ref="machine" v-model="form.machineId" clearable :fetch-suggestions="querySearchMachineAsync" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" @select="handleMachineSelect" @blur="handleMachineBlur" @clear="handleMachineClear" style="width: 100%"></el-autocomplete>
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
        var validatePassword = (rule, value, callback) => {
            if (value.trim() === '') {
                callback(new Error(this.$store.state.langData.cont.msg.validate.er0052))
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
            form: {
                mac: '',
                name: '',
                password: 'fidodarts',
                distributor: '',
                distributorId: '',
                store: '',
                storeId: '',
                machineId: '',
                description: `Raspberry Pi 4 Model B 8G\nSD:32G\nUbuntu 20.04.2 LTS (GNU/Linux 5.4.0-1034-raspi aarch64)`
            },
            selected: {
                distributor: {
                    value: '',
                    clear: false
                },
                store: {
                    distributorId: '',
                    value: '',
                    clear: false
                },
                machine: {
                    storeId: '',
                    value: '',
                    clear: false
                }
            },
            loading: false,
            rules: {
                mac: [
                    { validator: validateMac, required: true, trigger: 'no' }
                ],
                name: [
                    { validator: validateName, required: true, trigger: 'no' }
                ],
                password: [
                    { validator: validatePassword, required: true, trigger: 'no' }
                ],
                distributor: [
                    { validator: validateDistributor, required: true, trigger: 'no' }
                ]
            }
        }
    },
    created() {},
    methods: {
        querySearchAsync(queryString, cb) {
            var restaurants = []
            if (this.selected.distributor.clear) return
            axios
                .get('/api/getDistributorData', {
                    params: { name: queryString }
                })
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        data.data.map(iteam => {
                            var opt = {}
                            opt.value = iteam.value
                            opt.id = iteam.id
                            restaurants.push(opt)
                        })
                        cb(restaurants)
                    }
                }).catch(error => {
                    console.log(error)
                })
        },
        handleSelect(item) {
            this.form.distributorId = item.id
            this.form.distributor = this.selected.distributor.value = item.value
            this.checkStore()
        },
        handleBlur() {
            this.form.distributor = this.selected.distributor.value
        },
        handleClear() {
            this.$refs.distributor.$el.getElementsByTagName('input')[0].blur()
            this.form.distributor = this.selected.distributor.value = ''
            this.form.distributorId = ''
            this.handleStoreClear()
            this.selected.distributor.clear = true
            setTimeout(() => {
                this.selected.distributor.clear = false
            }, 500)
        },
        checkStore() {
            if (this.selected.store.distributorId !== this.form.distributorId) {
                this.handleStoreClear()
            }
        },
        querySearchStoreAsync(queryString, cb) {
            var restaurants = []
            if (this.selected.store.clear) return
            if (this.form.distributorId !== '') {
                axios
                    .get('/api/getStoreData', {
                        params: { distributorId: this.form.distributorId, name: queryString }
                    })
                    .then(response => {
                        let data = response.data.data
                        if (data.errorCode === 'er0000') {
                            data.data.map(iteam => {
                                var opt = {}
                                opt.value = iteam.value
                                opt.id = iteam.id
                                opt.distributorId = iteam.distributorId
                                restaurants.push(opt)
                            })
                            cb(restaurants)
                        }
                    }).catch(error => {
                        cb(restaurants)
                        console.log(error)
                    })
            } else {
                cb(restaurants)
            }
        },
        handleStoreSelect(item) {
            this.form.storeId = item.id
            this.form.store = this.selected.store.value = item.value
            this.selected.store.distributorId = item.distributorId
            console.log(item)
        },
        handleStoreBlur() {
            this.form.store = this.selected.store.value
        },
        handleStoreClear() {
            this.$refs.store.$el.getElementsByTagName('input')[0].blur()
            this.form.store = this.selected.store.value = ''
            this.form.storeId = ''
            this.selected.store.distributorId = ''
            this.selected.store.clear = true
            setTimeout(() => {
                this.selected.store.clear = false
            }, 500)
        },
        querySearchMachineAsync(queryString, cb) {
            var restaurants = []
            if (this.selected.machine.clear) return
            if (this.form.storeId !== '') {
                axios
                    .get('/api/getMachineData', {
                        params: { storeId: this.form.storeId, name: queryString }
                    })
                    .then(response => {
                        let data = response.data.data
                        if (data.errorCode === 'er0000') {
                            data.data.map(iteam => {
                                var opt = {}
                                opt.value = iteam.id
                                opt.id = iteam.id
                                opt.storeId = iteam.storeId
                                opt.distributorId = iteam.distributorId
                                restaurants.push(opt)
                            })
                            cb(restaurants)
                        }
                    }).catch(error => {
                        cb(restaurants)
                        console.log(error)
                    })
            } else {
                cb(restaurants)
            }
        },
        handleMachineSelect(item) {
            this.form.machineId = this.selected.machine.value = item.id
            this.selected.machine.storeId = item.storeId
        },
        handleMachineBlur() {
            this.form.machineId  = this.selected.machine.value
        },
        handleMachineClear() {
            this.$refs.machine.$el.getElementsByTagName('input')[0].blur()
            this.form.machineId = this.selected.machine.value = ''
            this.selected.machine.storeId = ''
            this.selected.machine.clear = true
            setTimeout(() => {
                this.selected.machine.clear = false
            }, 500)
        },
        checkMachine() {
            if (this.selected.machine.storeId !== this.form.storeId) {
                this.handleMachineClear()
            }
        },
        add() {
            this.$refs['ruleForm'].validate((valid) => {
                if (valid) {
                    this.loading = true
                    axios
                        .post('/api/addEquipment', this.form )
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
    }
}

</script>
