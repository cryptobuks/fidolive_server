<template>
    <div class="header no-user-select">
        <div class="h_left">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0,0,376,77">
                <image v-if="type === 'l'" xlink:href="../../../../../sass/images/stream_logo.png" height="100%" />
                <image v-else xlink:href="../../../../../sass/images/tournament_logo_hori.png" height="100%" />
                <!-- <text x="100" y="60" font-size="48" fill="#fff" letter-spacing="3">{{ fidoType }}{{ onlineMark }}</text> -->
            </svg>
        </div>
        <div class="h_center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0,0,1153,77">
                <text v-if="!(type === 'l' || type === 'tm')" x="50%" y="60" text-anchor="middle" font-size="42" fill="#fff" letter-spacing="3">
                    {{ name }}
                </text>
                <text v-else letter-spacing="3">
                    <tspan v-if="type === 'l'" x="50%" y="60" text-anchor="middle" font-size="42" fill="#fff">{{ name }} {{ groupName }} 第 {{ sequence }} 場</tspan>
                    <tspan v-else x="50%" y="60" text-anchor="middle" font-size="42" fill="#fff">{{ name }} {{ groupName }}〔 {{ roundName }} 〕</tspan>
                    <!-- <tspan x="50%" y="70" text-anchor="middle" font-size="24" fill="#16d9ff">{{ groupName }} ( 第 {{ sequence }} 場 )</tspan> -->
                </text>
            </svg>
            </svg>
        </div>
        <div class="h_right">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0,0,376,77">
                <text x="376" y="60" text-anchor="end" font-size="34" fill="#fff" letter-spacing="1" style="font-family: 'Noto Sans TC';">
                    {{ today }}
                </text>
            </svg>
        </div>
    </div>
</template>
<script>
export default {
    components: {},
    props: ['name', 'groupName', 'sequence', 'roundName', 'type', 'online', 'date'],
    sockets: {},
    data() {
        return {
            now: new Date()
        }
    },
    created() {
        setInterval(() => {
            this.now = new Date()
        }, 1000)
    },
    computed: {
        today() {
            /*switch (this.type) {
                case 'l':
                    return (new Date(this.date)).toString().split(' ').splice(1, 3).join(' ')
                    break
                default:
                    return this.now.toString().split(' ').splice(1, 4).join(' ')
                    break
            }*/
            return this.now.toString().split(' ').splice(1, 4).join(' ')
        },
        fidoType() {
            switch (this.type) {
                case 'l':
                    return 'FIDO LEAGE'
                    break
                default:
                    return 'FIDO TOURMENT'
                    break
            }
        },
        onlineMark() {
            return this.online === 1 ? ' ONLINE' : ''
        }
    },
    methods: {}
}

</script>
