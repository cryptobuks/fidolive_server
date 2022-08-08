<template>
    <div class="no-user-select text-center">
        <h1>{{ ename }}</h1>
    </div>
</template>
<script>
import { mapActions } from 'vuex'
export default {
    components: {},
    props: ['id'],
    data() {
        return {}
    },
    created() {
        var route = this.$route
        this.changeTitle({ title: 'default', name: route.name })
        this.fetchData()
    },
    mounted() {
    },
    computed: {
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
        }
    }
}

</script>
