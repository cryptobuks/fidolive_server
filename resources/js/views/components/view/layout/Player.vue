<template>
    <iframe :class="type" frameborder="0" scrolling="no" allow="autoplay" :src="src"></iframe>
</template>
<script>
export default {
    components: {},
    props: ['id', 'type'],
    data() {
        return {
            ports: [],
            awsUrl: 'http://fidolive.ga',
        }
    },
    created() {
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
        }
    },
    mounted() {},
    methods: {
        fetchData() {
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

    &.combination {
        height: calc(792/1920*100vw - 8px);
    }
}

</style>
