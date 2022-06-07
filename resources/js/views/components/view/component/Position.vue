<template>
    <div>
        <el-table :data="tableData" style="width: 100%">
            <el-table-column prop="no" label="No" width="80">
            </el-table-column>
            <el-table-column :label="$store.state.langData.cont.pageFn.table.Zoom">
                <template slot-scope="scope">
                    <span v-if="!scope.row.edit">{{ scope.row.zoom }}</span>
                    <el-input-number v-if="scope.row.edit" v-model="scope.row.zoom" @change="handleZoomChange" :min="1" :max="5" :step="0.1" size="mini"></el-input-number>
                </template>
            </el-table-column>
            <el-table-column prop="up" :label="$store.state.langData.cont.pageFn.table.Up">
            </el-table-column>
            <el-table-column prop="left" :label="$store.state.langData.cont.pageFn.table.Left">
            </el-table-column>
            <el-table-column :label="$store.state.langData.cont.pageFn.table.Operating" width="80">
                <template slot-scope="scope">
                    <el-button v-if="!scope.row.edit" @click.native.prevent="handleEdit(scope.$index, scope.row)" type="text">
                        {{ $store.state.langData.cont.pageFn.table.Edit }}
                    </el-button>
                    <el-button v-if="scope.row.edit" @click.native.prevent="handleOk(scope.$index, scope.row)" type="text">
                        {{ $store.state.langData.cont.pageFn.golbal.Save }}
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>
<script>
export default {
    components: {},
    data() {
        return {
            tableData: [{
                no: 20,
                zoom: 1,
                up: 0,
                left: 0,
                edit: false
            }, {
                no: 1,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 18,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 4,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 13,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 6,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 10,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 15,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 2,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 17,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 3,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 19,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 7,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 16,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 8,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 14,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 9,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 12,
                zoom: 1,
                up: 0,
                left: 0
            }, {
                no: 5,
                zoom: 1,
                up: 0,
                left: 0
            }]
        }
    },
    created() {},
    computed: {},
    methods: {
        handleEdit(index, row) {
            row.edit = true
        },
        handleOk(index, row) {

        },
        handleZoomChange(value) {
            console.log(value)
        }
    }
}

</script>
<style lang="scss">
.el-table {
    .el-table__cell {
        text-align: center !important;
    }
}

</style>
