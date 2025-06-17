<template>
    <tr>
        <td class="cell">{{ item.manager }} </td>
        <td class="cell">{{ getLocalDateTime(item.order_date) }}</td>
        <td class="cell">
            <TextInlineEdit :value="item.order_number"
                            :entityId="item.id"
                            :field="`order_number`"
                            :urlEdit="`order`"
                            :max="50"
                            :required="true"
                            @update="updateInline"
            />
        </td>
        <td class="cell">
            {{ item.vendor_code }}
        </td>
        <td class="cell">
            {{ item.goods_name }}
        </td>
        <td class="cell">
            <TextAreaInlineEdit :value="item.manager_comment"
                                :field="`manager_comment`"
                                :entityId="item.id"
                                :max="1000"
                                :urlEdit="`order`"
                                :required="true"
                                @update="updateInline"
            />
        </td>
        <td class="cell">
            <DecimalInlineEdit :value="item.sell_price"
                               :field="`sell_price`"
                               :entityId="item.id"
                               :urlEdit="`order`"
                               @update="updateInline"
            />
        </td>
        <td class="cell">
            <StatusInlineEdit :value="item.status"
                              :selected="item.status_alias"
                              :entityId="item.id"
                              @update="updateOption"
            />
        </td>
        <td class="cell">
            <IntegerInlineEdit :value="item.amount_in_order_paid"
                               :field="`amount_in_order_paid`"
                               :entityId="item.id"
                               :urlEdit="`order`"
                               @update="updateInline"
            />
        </td>
        <td class="cell">
            <DecimalInlineEdit :value="item.cost"
                               :field="`cost`"
                               :entityId="item.id"
                               :urlEdit="`order`"
                               @update="updateInline"
            />
        </td>
        <td class="cell">
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <a class="btn-sm app-btn-secondary" href="#" @click="addShipments(item.id)"> Приход </a>
                </div>
                <div class="col-auto">
                    <a class="btn-sm app-btn-secondary" href="#" @click="showShipments(item.id)"> Історія </a>
                </div>
            </div>
        </td>
        <td class="cell">{{ item.shipments_amount }}</td>
        <td class="cell">{{ item.remainder }}</td>
        <td class="cell">
            <ProviderInlineEdit :value="item.provider_start"
                                :selected="item.provider_start_id"
                                :entityId="item.id"
                                @update="updateOption"
            />
        </td>
        <td class="cell">
            <DateInlineEdite :value="item.date_check"
                             :field="`date_check`"
                             :entityId="item.id"
                             :urlEdit="`order`"
                             @update="updateInline"
            />
        </td>

        <td class="cell">
            <TextAreaInlineEdit :value="item.comment"
                                :field="`comment`"
                                :entityId="item.id"
                                :max="1000"
                                :urlEdit="`order`"
                                :required="true"
                                @update="updateInline"
            />
        </td>

        <td class="cell">
            <DefectInlineEdit :value="item.defect"
                              :selected="item.defect_alias"
                              :entityId="item.id"
                              @update="updateOption"
            />
        </td>

        <td class="cell">
            <TextInlineEdit :value="item.comfy_code"
                            :field="`comfy_code`"
                            :entityId="item.id"
                            :max="50"
                            :urlEdit="`order`"
                            :required="item.provider_type === comfyProviderComputed"
                            @update="updateInline"
            />
        </td>
        <td class="cell">
            <TextInlineEdit :value="item.comfy_goods_name"
                            :field="`comfy_goods_name`"
                            :entityId="item.id"
                            :max="150"
                            :urlEdit="`order`"
                            :required="item.provider_type === comfyProviderComputed"
                            @update="updateInline"
            />
        </td>
        <td class="cell">
            <ComfyBrandEdit
                :value="item.comfy_brand"
                :selected="item.comfy_brand_id"
                :entityId="item.id"
                @update="updateOption"
            />
        </td>
        <td class="cell">
            <TextInlineEdit :value="item.comfy_category"
                            :field="`comfy_category`"
                            :entityId="item.id"
                            :max="100"
                            :urlEdit="`order`"
                            :required="item.provider_type === comfyProviderComputed"
                            @update="updateInline"
            />
        </td>
        <td class="cell">
            <DecimalInlineEdit :value="item.comfy_price"
                               :field="`comfy_price`"
                               :entityId="item.id"
                               :urlEdit="`order`"
                               @update="updateInline"
            />
        </td>
        <td class="cell">
            {{ item.comfy_price_cost }}
        </td>
        <td class="cell">
            <router-link class="btn-sm app-btn-secondary" :to="`/delivery-order/edit/${item.id}`">
                Edit
            </router-link>
        </td>
    </tr>
</template>

<script lang="ts">
import {defineComponent, defineAsyncComponent, PropType} from "vue";
import {InlineEdit, InlineOptionEdit} from "./InlineEdit/Types/InlineEdit";
import {OrderType} from "./Type/OrderType";
import {getLocalDateTime} from "../../../../../common/helpers/DateTime";
import {ProvidersEnum} from "@/js/src/modules/Admin/pages/Providers/enum/ProvidersEnum";

const TextInlineEdit = defineAsyncComponent(() => import('@/js/src/modules/Orders/pages/List/components/InlineEdit/TextInlineEdit.vue'));
const TextAreaInlineEdit = defineAsyncComponent(() => import('@/js/src/modules/Orders/pages/List/components/InlineEdit/TextAreaInlineEdit.vue'));
const DecimalInlineEdit = defineAsyncComponent(() => import('@/js/src/modules/Orders/pages/List/components/InlineEdit/DecimalInlineEdit.vue'));
const IntegerInlineEdit = defineAsyncComponent(() => import('@/js/src/modules/Orders/pages/List/components/InlineEdit/IntegerInlineEdit.vue'));
const StatusInlineEdit = defineAsyncComponent(() => import('@/js/src/modules/Orders/pages/List/components/InlineEdit/StatusInlineEdit.vue'));
const ProviderInlineEdit = defineAsyncComponent(() => import('@/js/src/modules/Orders/pages/List/components/InlineEdit/ProviderInlineEdit.vue'));
const DefectInlineEdit = defineAsyncComponent(() => import('@/js/src/modules/Orders/pages/List/components/InlineEdit/DefectInlineEdit.vue'));
const DateInlineEdite = defineAsyncComponent(() => import('@/js/src/modules/Orders/pages/List/components/InlineEdit/DateInlineEdite.vue'));
const ComfyBrandEdit = defineAsyncComponent(() => import("@/js/src/modules/Orders/pages/List/components/InlineEdit/ComfyBrandEdit.vue"));

export default defineComponent({
    name: "TableRow",
    components: {
        TextInlineEdit,
        TextAreaInlineEdit,
        DecimalInlineEdit,
        IntegerInlineEdit,
        StatusInlineEdit,
        ProviderInlineEdit,
        DefectInlineEdit,
        DateInlineEdite,
        ComfyBrandEdit,
    },
    props: {
        value: {
            type: Object as PropType<OrderType>,
            required: true,
        },
    },
    data() {
        return {
            item: {} as OrderType
        };
    },
    computed: {
        comfyProviderComputed() {
            return ProvidersEnum.COMFY;
        }
    },
    created() {
        this.item = this.value;
    },
    methods: {
        getLocalDateTime(date: string): string {
            return getLocalDateTime(date);
        },
        addShipments(id) {
            this.$emit('addShipments', id);
        },
        showShipments(id) {
            this.$emit('showShipments', id);
        },
        updateInline(dto: InlineEdit): void {
            this.item[dto.field] = dto.value;
            if (dto.field === 'comfy_price' || dto.field === 'cost') {
                this.item.comfy_price_cost = this.item.comfy_price - this.item.cost;
            }
            this.$emit('updateInline', dto);
        },
        updateOption(dto: InlineOptionEdit): void {
            this.item[dto.field] = dto.label;
            if (dto.field === 'comfy_brand') {
                this.item[`${dto.field}_id`] = dto.value;
                return;
            }
            if (dto.field === 'defect') {
                this.item[`${dto.field}_id`] = dto.value;
                return;
            }
            this.item[`${dto.field}_alias`] = dto.value;
        }
    }
});

</script>

<style scoped>

</style>
