<template>
    <div class="no-user-select text-center" style="background: rgba(0,0,0,0);">
        <h1>{{ ename }}</h1>
        <iframe :class="type" ref="iframe" frameborder="0" scrolling="no" allow="autoplay" :src="src"></iframe>
    </div>
</template>
<script>
import { mapActions } from 'vuex'
export default {
    components: {},
    props: ['id', 'type', 'audio'],
    data() {
        return {
            ports: [],
            awsUrl: 'http://fidolive.ga',
            name: null,
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
                url = `${this.awsUrl}:${p_80.port_no}/${this.type}?p_8000=${p_8000.port_no}&audio=${this.audio}`
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
    mounted() {},
    methods: {
        ...mapActions(['changeTitle']),
        fetchData() {
            this.getPort()
            this.getName()
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
        }
    }
}

</script>
<style scoped lang="scss">
iframe {
    width: 100%;

    &.pc {
        height: calc(720/1280*100vw);
    }

    &.dartboard {
        height: calc(896/1075*100vw);
    }

    &.combination {
        height: calc(792/1920*100vw + 14px);
    }
}

</style>
