<template>
    <div class="no-user-select pv">
        <iframe frameborder="0" scrolling="no" allow="autoplay" :src="src"></iframe>
    </div>
</template>
<script>
export default {
    components: {},
    props: ['id'],
    data() {
        return {
            ports: []
        }
    },
    created() {
        this.fetchData()
    },
    computed: {
        src() {
            var url =''
            var p_80 = this.ports.find(item => {
                return item.port_name === 80
            })
            var p_8000 = this.ports.find(item => {
                return item.port_name === 8000
            })
            console.log(p_80)
            if(p_80 && p_8000) {
                url = `http:fidolive.ga:${p_80.port_no}/pc?p_8000=${p_8000.port_no}`
            }
            return url
        }
    },
    mounted() {},
    methods: {
        fetchData() {
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
</style>
<style scoped>
