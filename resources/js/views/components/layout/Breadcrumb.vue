<template>
    <div style="margin-bottom: 1.25rem;">
        <el-breadcrumb separator="|">
            <template v-for="(item, index) in iteams">
                <el-breadcrumb-item v-if="item.hasback" :to="{ name: item.name }" :key="index">
                    <i class="el-icon-arrow-left"></i>
                    {{ $store.state.langData.cont.pageFn.breadcrumb.Back }}
                </el-breadcrumb-item>
                <el-breadcrumb-item v-else :key="index">
                    {{ item.title }}
                </el-breadcrumb-item>
            </template>
        </el-breadcrumb>
    </div>
</template>
<script>
export default {
    props: ['iteams'],
}

</script>
