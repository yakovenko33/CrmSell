<template>
    <div :class="[$style.custom__popup, 'modal fade show']" id="exampleModalCenter"  tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 600px" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Добавити прихід</h5>
                    <button v-if="!isLoading" type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeButton">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div v-if="!isLoading">
                        <div class="app-card app-card-orders-table shadow-sm mb-5">
                            <div class="app-card-body">
                                <div class="table-responsive">
                                    <table class="table app-table-hover mb-0 text-left">
                                        <HeadTable
                                            :headColumns="headColumns"
                                            :sortData="sortData"
                                            @sort="clickSort"
                                        />
                                        <tbody>
                                        <template v-for="item in records">
                                            <tr>
                                                <td class="cell">{{ item.created_by }}</td>
                                                <td class="cell">{{ getLocalDateTime(item.created_at) }}</td>
                                                <td class="cell">{{ item.shipment_date }}</td>
                                                <td class="cell">{{ item.amount }}</td>
                                            </tr>
                                        </template>
                                        </tbody>

                                    </table>
                                </div><!--//table-responsive-->

                            </div><!--//app-card-body-->
                        </div><!--//app-card-->
                        <Pagination
                            :pagination="pagination"
                            @clickPagination="refreshRecords"
                        />
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script lang="ts">

import {defineAsyncComponent, defineComponent} from "vue";
const HeadTable = defineAsyncComponent(() => import("@/js/src/common/components/Table/HeadTable.vue"));
const Pagination = defineAsyncComponent(() => import('@/js/src/common/components/Table/Pagination.vue'));
import pagination from "../../../../../common/components/Table/mixins/Pagination";
import {HeadColumn} from "../../../../../common/components/Table/Type/HeadColumn";
import {SortData} from "../../../../../common/components/Table/Type/SortData";
import axios from "axios";
import {getLocalDateTime} from "../../../../../common/helpers/DateTime";

interface HistoryShipmentsType {
    created_by: string;
    created_at: string;
    shipment_date: string;
    amount: number;
}

export default defineComponent({
    name: "HistoryShipments",
    mixins: [pagination],
    components: {
        HeadTable,
        Pagination
    },
    props: {
        orderId: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            isLoading: false,
            headColumns: [
                {name: 'created_by', translate: 'Ким створенно', sort: true},
                {name: 'created_at', translate: 'Дата создания', sort: true},
                {name: 'shipment_date', translate: 'Дата відвантаження', sort: true},
                {name: 'amount', translate: 'Кількість', sort: true},
            ] as HeadColumn[],
            sortData: {
                sortField: 'created_at',
                sortDir: 'desc',
            },
            filter: {},
            records: [] as HistoryShipmentsType[],
        }
    },
    created() {
        this.getData();
    },
    methods: {
        closeButton(): void {
            this.$emit('closeButton');
        },
        getData(): void {
            this.isLoading = true;
            axios.get('/api/v1/shipments-history', {
                params: {
                    pageNumber: this.pagination.pages.current_page,
                    sortDir: this.sortData.sortDir,
                    sortField: this.sortData.sortField,
                    parentId: this.orderId,
                }
            }).then((response) => {
                if (response.status === 200) {
                    const result = response.data.data;
                    this.pagination = result.pagination;
                    this.records = result.records;
                } else {
                    alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
                }
                this.isLoading = false;
            }).catch(() => {
                this.isLoading = false;
            });
        },
        clickSort(sortData: SortData) {
            this.pagination.pages.current_page = 1;
            this.sortData = sortData;
            this.getData();
        },
        refreshRecords(page: number): void {
            this.pagination.pages.current_page = page;
            this.getData();
        },
        getLocalDateTime(date: string): string {
            return getLocalDateTime(date);
        },
    }
});

</script>

<style module>
.custom__popup {
    display: block;
    background-color: rgba(0, 0, 0, 0.5);
}
</style>
