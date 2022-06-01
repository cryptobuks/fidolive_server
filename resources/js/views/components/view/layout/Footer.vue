<template>
    <div class="footer">
        <div :class="['f_left', data[0].status ? 'active' : '']" @click="changShowModel('home')">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0,0,382,84">
                <text x="20" y="50" font-size="42" letter-spacing="3">{{ data[0].track }}</text>
                <text x="10" y="70" font-size="16" letter-spacing="3">{{ uId(data[0].u_name) }}</text>
                <text x="362" y="46" font-size="32" text-anchor="end" letter-spacing="3">{{ uName(data[0].u_name) }}</text>
                <text x="372" y="70" font-size="16" text-anchor="end" letter-spacing="3">{{ data[0].p_name }}</text>
            </svg>
        </div>
        <div class="f_center" @click="changShowModel('tree')">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0,0,193,85">
                
            </svg>
        </div>
        <div :class="['f_right', data[1].status ? 'active' : '']" @click="changShowModel('away')">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0,0,382,84">
                <text x="20" y="50" font-size="42" letter-spacing="3">{{ data[1].track }}</text>
                <text x="20" y="70" font-size="16" letter-spacing="3">{{ uId(data[1].u_name) }}</text>
                <text x="362" y="46" font-size="32" text-anchor="end" letter-spacing="3">{{ uName(data[1].u_name) }}</text>
                <text x="372" y="70" font-size="16" text-anchor="end" letter-spacing="3">{{ data[1].p_name }}</text>
            </svg>
        </div>
    </div>
</template>
<script>
export default {
    components: {},
    props: ['data'],
    sockets: {},
    data() {
        return {}
    },
    created() {},
    computed: {},
    methods: {
        changShowModel(type) {
            this.$emit('changShow', type);
        },
        uId(name) {
            var ns = name.split(" ")
            return ns[1]
        },
        uName(name) {
            var ns = name.split(" ")
            return ns[0]
        }
    }
}

</script>
