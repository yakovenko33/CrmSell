<template>
    <thead>
        <tr>
            <template v-for="column in headColumns">
                <th class="cell" :style="[column.sort ? 'cursor: pointer' : '']" @click="sort(column.name)">
                    <div :style="column.hasOwnProperty('width') ? column.width : ``">
                        {{ column.translate }}
                        <img v-if="column.sort" class="logo-icon me-2"  :src="getIcon(column)" alt="logo">
                    </div>
                </th>
            </template>
        </tr>
    </thead>
</template>

<script lang="ts">

import {defineComponent, PropType, ref} from 'vue';
import {SortData} from "./Type/SortData";
import {HeadColumn} from "./Type/HeadColumn";
import sort from '@/assets/images/sort/sort.png';
import sortDown from '@/assets/images/sort/sort-down.png';
import sortUp from '@/assets/images/sort/sort-up.png';

export default defineComponent({
    name: "HeadTable",
    props: {
        headColumns: {
            type: Array as PropType<HeadColumn[]>,
            required: true,
        },
        sortData: {
            type: Object as PropType<SortData>,
            required: true,
        },
        columnDefs: {
            type: Object,
            default: () => {
                return {
                    name: "name",
                    vname: "vname"
                }
            }
        },
    },
    data() {
        return {
            sortIcon: {
                default: sort,
                asc: sortDown ,
                desc: sortUp,
            }
        }
    },
    methods: {
        getIcon(item: HeadColumn) {
            let icon = '';
            if (item.name === this.sortData.sortField) {
                icon = (this.sortData.sortDir === 'asc') ? 'asc' : 'desc'
            } else {
                icon = 'default';
            }
            return this.sortIcon[icon];
        },
        sort(column: string): void {
            const currentColumn = this.headColumns.find((data, index) => data[this.columnDefs.name] === column);
            if (currentColumn.sort === undefined || currentColumn.sort) {
                let sortDir = 'desc';
                if (this.sortData.sortField === column) {
                    sortDir = (this.sortData.sortDir === 'asc') ? 'desc' : 'asc';
                }
                const sortData = {
                    sortField: column,
                    sortDir: sortDir,
                } as SortData;
                this.$emit('sort', sortData);
            }
        }
    }
});

</script>

<style scoped>

</style>
