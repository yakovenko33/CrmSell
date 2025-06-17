<template>
    <div class="tab-pane fade show active" id="providers-all" role="tabpanel" aria-labelledby="orders-all-tab">
        <div>
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
                            <template v-if="records.length > 0">
                                <template v-for="item in records">
                                    <TableRow :value="item"
                                              @addShipments="addShipments"
                                              @showShipments="showShipments"
                                              @updateInline="updateInline"
                                              @updateOption="updateOption"
                                    />
                                </template>
                            </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <Pagination
                :pagination="paginationProp"
                @clickPagination="refreshRecords"
            />
        </div>
        <template v-if="addShipmentModal">
            <AddShipment
                :orderId="this.orderId"
                @addShipments="addShipmentsToCRM"
                @closeButton="addShipmentClose"
            />
        </template>
        <template v-if="showHistoryShipments">
            <HistoryShipments
                :orderId="this.orderId"
                @closeButton="historyShipmentsClose"
            />
        </template>
    </div>
</template>

<script lang="ts">

import {defineAsyncComponent, defineComponent} from "vue";
import {HeadColumn} from "../../../../common/components/Table/Type/HeadColumn";
import {SortData} from "../../../../common/components/Table/Type/SortData";

import {getLocalDateTime} from "../../../../common/helpers/DateTime";
import {InlineEdit, InlineOptionEdit} from "./components/InlineEdit/Types/InlineEdit";
import {OrderType} from "./components/Type/OrderType";
import {PropType} from "vue/dist/vue";
import {FilterType} from "./Types/FilterType";

const HeadTable = defineAsyncComponent(() => import("@/js/src/common/components/Table/HeadTable.vue"));
const Pagination = defineAsyncComponent(() => import('@/js/src/common/components/Table/Pagination.vue'));
const AddShipment = defineAsyncComponent(() => import('@/js/src/modules/Orders/pages/List/components/AddShipments.vue'));
const HistoryShipments = defineAsyncComponent(() => import('@/js/src/modules/Orders/pages/List/components/HistoryShipments.vue'));
const TableRow = defineAsyncComponent(() => import('@/js/src/modules/Orders/pages/List/components/TableRow.vue'));



export default defineComponent({
    name: "OrdersTable",
    components: {
        HeadTable,
        Pagination,
        AddShipment,
        HistoryShipments,
        TableRow
    },
    props: {
        filterParams: {
            type: Object as PropType<FilterType>,
            required: true,
        },
        paginationProp: {
            type: Object,
            required: true,
        },
        recordsProp: {
            type: Array,
            required: true,
        },
        sortDataProp: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            headColumns: [
                {name: 'manager', translate: 'Менеджер', sort: true, width: 'width: 125px'},
                {name: 'order_date', translate: 'Дата', sort: true, width: 'width: 125px'},
                {name: 'order_number', translate: '№ Замовлення', sort: true, width: 'width: 150px'},
                {name: 'vendor_code', translate: 'Артикул', sort: false, width: 'width: 100px'},
                {name: 'goods_name', translate: 'Товар', sort: true, width: 'width: 100px'},
                {name: 'manager_comment', translate: 'Коментар/уточнення постачальника', sort: false, width: 'width: 200px'},
                {name: 'sell_price', translate: 'Ціна', sort: true, width: 'width: 100px'},
                {name: 'status', translate: 'Статус замовлення', sort: true, width: 'width: 100px'},
                {name: 'amount_in_order_paid', translate: 'К-ть оплачених', sort: true, width: 'width: 100px'},
                {name: 'cost', translate: 'Ціна закупки', sort: true, width: 'width: 100px'},
                {name: 'date_of_shipment', translate: 'Дата відвантаження', sort: false, width: 'width: 200px'},
                {name: 'shipments_amount', translate: 'К-ть забраних', sort: true, width: 'width: 100px'},
                {name: 'remainder', translate: 'Залишок', sort: true, width: 'width: 100px'},
                {name: 'provider_start', translate: 'Постачальник', sort: false, width: 'width: 150px'},
                {name: 'date_check', translate: 'Дата Чеку', sort: false, width: 'width: 100px'},
                {name: 'comment', translate: 'Коментар', sort: false, width: 'width: 200px'},
                {name: 'defect', translate: 'Списаний', sort: true, width: 'width: 100px'},
                {name: 'comfy_code', translate: 'Код номенклатуры', sort: true, width: 'width: 150px'},
                {name: 'comfy_goods_name', translate: 'Наименование продукта', sort: true, width: 'width: 150px'},
                {name: 'comfy_brand', translate: 'Наименование бренда', sort: true, width: 'width: 150px'},
                {name: 'comfy_category', translate: 'Наименование категории', sort: true, width: 'width: 150px'},
                {name: 'comfy_price', translate: 'Цена/значение', sort: true, width: 'width: 150px'},
                {name: 'comfy_price_cost', translate: 'Цена/значение - Ціна закупки', sort: true, width: 'width: 150px'},
                {name: 'view_edit', translate: '', sort: false}
            ] as HeadColumn[],
            orderId: '',
            addShipmentModal: false,
            showHistoryShipments: false,
            sortData: {
                sortField: 'created_at',
                sortDir: 'desc',
            },
            records: [] as OrderType[],
        }
    },
    created() {
        this.records = this.recordsProp;
        this.sortData = this.sortDataProp;
    },
    methods: {
        clickSort(sortData: SortData) {
            this.sortData = sortData;
            this.$emit("clickSort", this.sortData);
        },
        refreshRecords(page: number): void {
            this.$emit("refreshRecords", page);
        },
        getLocalDateTime(date: string): string {
            return getLocalDateTime(date);
        },
        addShipments(id: string) {
            this.orderId = id;
            this.addShipmentModal = !this.addShipmentModal;
        },
        showShipments(id: string) {
            this.orderId = id;
            this.showHistoryShipments = !this.showHistoryShipments;
        },
        addShipmentClose(): void {
            this.addShipmentModal = !this.addShipmentModal;
        },
        historyShipmentsClose(): void {
            this.showHistoryShipments = !this.showHistoryShipments;
        },
        addShipmentsToCRM(): void {
            this.addShipmentModal = !this.addShipmentModal;
            this.$emit('addShipmentsToCRM');
        },
        updateInline(dto: InlineEdit): void {
            const index = this.records.findIndex(item => item.id === dto.entityId);
            this.records[index][dto.field] = dto.value;
        },
        updateOption(dto: InlineOptionEdit): void {
            const index = this.records.findIndex(item => item.id === dto.entityId);
            this.item[dto.field] = dto.label;
            if (dto.field === 'defect') {
                this.records[index][`${dto.field}_id`] = dto.value;
            } else {
                this.records[index][`${dto.field}_alias`] = dto.value;
            }
        }
    }
});

</script>

<style scoped>

</style>
