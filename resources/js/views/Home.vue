<template>
    <div>
    	<router-view />
    </div>
</template>
<script>
import { mapActions } from 'vuex'
export default {
    components: {},
    data() {
        return {}
    },
    created() {
    	/*var route = this.$route
        console.log(route)
        this.changeTitle({ title: 'default', name: route.name })*/
    },
    methods: {
    	//...mapActions(['changeTitle'])
    }
}

</script>
