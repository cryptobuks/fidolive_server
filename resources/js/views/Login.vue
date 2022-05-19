<template>
    <el-row type="flex" justify="center" align="middle" class="login">
        <el-col :xs="20" :sm="8" :md="7" :lg="5" :xl="4">
            <!-- <img src="images/logo_hen.png" alt="" class="logo"> -->
            <h1>{{ $store.state.langData.cont.pageFn.login.label }}</h1>
            <el-form :model="ruleForm" status-icon :rules="rules" ref="ruleForm" label-position="top">
                <el-form-item label="Account" prop="account">
                    <el-input v-model="ruleForm.account"></el-input>
                </el-form-item>
                </el-form-item>
                <el-form-item label="Password" prop="pass">
                    <el-input type="password" v-model="ruleForm.pass" @keyup.enter.native="submitForm"></el-input>
                </el-form-item>
                <br>
                <el-form-item>
                    <el-button type="primary" :loading="loading" @click="submitForm">{{ $store.state.langData.cont.pageFn.login.submit }}</el-button>
                </el-form-item>
            </el-form>
        </el-col>
    </el-row>
</template>
<script>
import { mapState, mapGetters, mapActions } from 'vuex'
export default {
    components: {},
    data() {
        var validateAccount = (rule, value, callback) => {
            if (value === '') {
                callback(new Error(this.$store.state.langData.cont.msg.validate.er0001))
            } else {
                callback()
            }
        }
        var validatePass = (rule, value, callback) => {
            if (value === '') {
                callback(new Error(this.$store.state.langData.cont.msg.validate.er0002))
            } else {
                if (this.ruleForm.error === true) {
                    callback(new Error(this.$store.state.langData.cont.msg.validate.er0003))
                }
                callback()
            }
        }
        return {
            ruleForm: {
                account: 'nvtek@gmail.com',
                pass: '',
                error: false
            },
            rules: {
                account: [
                    { validator: validateAccount, required: true, trigger: 'no' }
                ],
                pass: [
                    { validator: validatePass, required: true, trigger: 'no' }
                ]
            },
            loading: false
        }
    },
    computed: {
        ...mapGetters(['isTouchDevice'])
    },
    created() {
        let pageName = this.$store.state.langData.cont.pageName
        this.changeTitle(`${pageName.title.admin}::${pageName.login}`)
    },
    methods: {
        ...mapActions(['changeTitle', 'changeLoginStatus', 'setMe']),
        submitForm() {
            this.loading = true
            this.$refs['ruleForm'].validate((valid) => {
                if (valid) {
                    axios.post('/api/login', this.ruleForm)
                        .then(response => {
                            let data = response.data.data
                            if (data.errorCode === 'er0000') {
                                localStorage.setItem('id', data.data)
                                var id = Base64.decode(localStorage.getItem('id'))
                                var ids = id.split(',')
                                this.setMe({
                                    id: ids[0],
                                    roleID: ids[1],
                                    roleCode: ids[2] * 1
                                })
                                this.changeLoginStatus(false)
                                this.$router.push({ path: '/' })
                            } else {
                                this.loading = false
                                this.ruleForm.error = true
                                this.$refs['ruleForm'].clearValidate()
                                this.$refs['ruleForm'].validateField('pass', (valid) => {
                                    this.ruleForm.pass = ''
                                    setTimeout(() => {
                                        this.$refs['ruleForm'].clearValidate()
                                        this.ruleForm.error = false
                                    }, 3000)
                                })
                            }
                        })
                        .catch(error => { // 請求失敗處理
                        })
                } else {
                    this.loading = false
                    return false
                }
            })
        }
    }
}

</script>
<style scoped lang="scss">
.login {
    height: 100vh;

    .logo {
        width: 100%;
    }

    ;
}

</style>
