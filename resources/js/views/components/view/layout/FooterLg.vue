<template>
    <div class="footer no-user-select">
        <div :class="['f_left', data[0].status[1] ? 'active' : '']" @click="changShowModel(0)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 407.355 111.5">
                <g transform="translate(-448.243 -952.25)">
                    <rect class="a" width="405.855" height="110" rx="5" transform="translate(448.993 953)" />
                    <path class="b" d="M405.855,22.129H0V5A5.006,5.006,0,0,1,5,0H400.855a5.006,5.006,0,0,1,5,5V22.128Zm-11.9-14.181h0l-3.377,3.116,3.377,3.117,3.376-3.117-3.376-3.116Zm-382.777,0h0L7.8,11.065l3.376,3.117,3.377-3.117L11.18,7.949Z" transform="translate(449.138 953)" />
                    <rect class="c" width="59" height="1" transform="translate(464.187 1048.095)" />
                    <rect class="c" width="4.815" height="4.815" transform="translate(493.591 1045.19) rotate(45)" />
                </g>
                <text class="d" x="25" y="18" font-size="16" letter-spacing="3">{{ data[0].teamName }}</text>
                <text class="c" x="47.5" y="75" font-size="28" text-anchor="middle">主隊</text>
                <text v-if="data[0].row !== -1" class="c" x="60%" y="65" text-anchor="middle">
                    <tspan v-if="data[0].player.length > 2" font-size="24">
                        {{ data[0].player[data[0].row] }}
                    </tspan>
                    <tspan v-else :font-size="data[0].row === 0 ? 24 : 16">
                        {{ data[0].player[0] }}
                    </tspan>
                    <tspan v-if="data[0].player.length > 1" font-size="24" letter-spacing="3">
                        /
                    </tspan>
                    <tspan v-if="data[0].player.length > 2" font-size="16">
                        {{ data[0].player.length }} 人賽
                    </tspan>
                    <tspan v-else-if="data[0].player.length > 1" :font-size="data[0].row === 1 ? 24 : 16">
                        {{ data[0].player[1] }}
                    </tspan>
                </text>
                <text v-else class="c" x="60%" y="65" font-size="24" text-anchor="middle">{{ data[0].teamName }}</text>
                <text class="c" x="60%" y="90" font-size="16" text-anchor="middle">{{ data[0].storeName }}</text>
                <circle v-if="first===0" cx="12.5" cy="35" r="4" fill="#EBDA6B" />
            </svg>
        </div>
        <div class="f_center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 284 173">
                <text v-if="!gameStatus" class="c" x="50%" y="120" font-size="100" text-anchor="middle">VS</text>
                <g v-else>
                    <template v-if="set.length > 0">
                        <text x="30%" y="60" font-size="64" fill="#EBDA6B" text-anchor="end">{{ set[0] }}</text>
                        <text x="30%" y="150" font-size="64" fill="#EBDA6B" text-anchor="end">{{ leg[0] }}</text>
                        <text x="50%" y="60" font-size="54" text-anchor="middle" fill="#FFFFFF">SET</text>
                        <text x="50%" y="150" font-size="54" text-anchor="middle" fill="#FFFFFF">LEG</text>
                        <text x="70%" y="60" font-size="64" fill="#EBDA6B" text-anchor="start">{{ set[1] }}</text>
                        <text x="70%" y="150" font-size="64" fill="#EBDA6B" text-anchor="start">{{ leg[1] }}</text>
                    </template>
                    <template v-else>
                        <text x="30%" y="110" font-size="64" fill="#EBDA6B" text-anchor="end">{{ leg[0] }}</text>
                        <text class="c" x="50%" y="110" font-size="54" text-anchor="middle">LEG</text>
                        <text x="70%" y="110" font-size="64" fill="#EBDA6B" text-anchor="start">{{ leg[1] }}</text>
                    </template>
                </g>
            </svg>
        </div>
        <div :class="['f_right', data[1].status[1] ? 'active' : '']" @click="changShowModel(1)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 407.355 111.5">
                <g transform="translate(-448.243 -952.25)">
                    <rect class="a" width="405.855" height="110" rx="5" transform="translate(448.993 953)" />
                    <path class="b" d="M405.855,22.129H0V5A5.006,5.006,0,0,1,5,0H400.855a5.006,5.006,0,0,1,5,5V22.128Zm-11.9-14.181h0l-3.377,3.116,3.377,3.117,3.376-3.117-3.376-3.116Zm-382.777,0h0L7.8,11.065l3.376,3.117,3.377-3.117L11.18,7.949Z" transform="translate(449.138 953)" />
                    <rect class="c" width="59" height="1" transform="translate(464.187 1048.095)" />
                    <rect class="c" width="4.815" height="4.815" transform="translate(493.591 1045.19) rotate(45)" />
                </g>
                <text class="d" x="25" y="18" font-size="16" letter-spacing="3">{{ data[1].teamName }}</text>
                <text class="c" x="47.5" y="75" font-size="28" text-anchor="middle">客隊</text>
                <text v-if="data[1].row !== -1" class="c" x="60%" y="65" text-anchor="middle">
                    <tspan v-if="data[1].player.length > 2" font-size="24">
                        {{ data[1].player[data[1].row] }}
                    </tspan>
                    <tspan v-else :font-size="data[1].row === 0 ? 24 : 16">
                        {{ data[1].player[0] }}
                    </tspan>
                    <tspan v-if="data[1].player.length > 1" font-size="24" letter-spacing="3">
                        /
                    </tspan>
                    <tspan v-if="data[1].player.length > 2" font-size="16">
                        {{ data[1].player.length }} 人賽
                    </tspan>
                    <tspan v-else-if="data[1].player.length > 1" :font-size="data[1].row === 1 ? 24 : 16">
                        {{ data[1].player[1] }}
                    </tspan>
                </text>
                <text v-else class="c" x="60%" y="65" font-size="24" text-anchor="middle">{{ data[1].teamName }}</text>
                <text class="c" x="60%" y="90" font-size="16" text-anchor="middle">{{ data[1].storeName }}</text>
                <circle v-if="first===1" cx="12.5" cy="35" r="4" fill="#EBDA6B" />
            </svg>
        </div>
    </div>
</template>
<script>
export default {
    components: {},
    props: ['data', 'gameStatus', 'first', 'set', 'leg'],
    sockets: {},
    data() {
        return {}
    },
    created() {},
    computed: {},
    methods: {
        changShowModel(type) {
            this.$emit('changShow', type);
        }
    }
}

</script>
