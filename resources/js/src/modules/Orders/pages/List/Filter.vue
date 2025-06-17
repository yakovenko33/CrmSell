<template>
    <div :class="[$style.custom__popup, 'modal fade show']" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Filter</h5>
                    <button v-if="!isLoading" type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeButton">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" v-if="!isLoading">
                    <div class="form-group row">
                        <div class="form-group col-md-6">
                            <label for="comfyCategory">Менеджер</label>
                            <select class="form-select" name="value" v-model="filter.manager">
                                <template v-for="item in managersOptions">
                                    <option :selected="item.key === filter.manager" :value="item.key">{{ item.value }}</option>
                                </template>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="comfyCategory">Статус замовлення</label>
                            <select multiple class="form-select" name="value" v-model="filter.status">
                                <template v-for="item in statusOptions">
                                    <option :selected="filter.status.includes(item.key)" :value="item.key">{{ item.value }}</option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="form-group col-md-6">
                            <label for="comfyCategory">Дата від</label>
                            <input name="order_date_from" type="date" class="form-control" v-model="filter.order_date_from">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="order_date_to">Дата до</label>
                            <input name="order_date_to" type="date" class="form-control" v-model="filter.order_date_to">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="form-group col-md-6">
                            <label for="comfyCategory">Дата Чеку від</label>
                            <input name="order_date_from" type="date" class="form-control" v-model="filter.date_check_from">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="order_date_to">Дата Чеку до</label>
                            <input name="order_date_to" type="date" class="form-control" v-model="filter.date_check_to">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="form-group col-md-6">
                            <label for="vendorCode">Артикул</label>
                            <input name="vendorCode" class="form-control" type="text" v-model="filter.vendor_code" @input="searchByVendorCode">
                            <template v-if="vendorCodeList.length > 0">
                                <select class="form-select" v-model="vendorCodeValueComputed" size="5">
                                    <template v-for="item in vendorCodeList">
                                        <option :value="item.id">{{ item.vendor_code }}</option>
                                    </template>
                                </select>
                            </template>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="=goodsName">Товар</label>
                            <input name="goodsName" class="form-control" type="text" v-model="filter.goods_name" @input="searchByGoodsName">
                            <template v-if="goodsNameList.length > 0">
                                <select class="form-select" v-model="goodsNameValueComputed" size="5">
                                    <template v-for="item in goodsNameList">
                                        <option :value="item.id">{{ item.name }}</option>
                                    </template>
                                </select>
                            </template>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="form-group col-md-6">
                            <label>Постачальник</label>
                            <select class="form-select" name="provider_start" v-model="filter.provider_start">
                                <template v-for="item in providerOptions">
                                    <option :selected="item.key === filter.provider_start" :value="item.key">{{ item.value }}</option>
                                </template>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Списаний</label>
                            <select class="form-select" name="defect" v-model="filter.defect">
                                <template v-for="item in defectsOptions">
                                    <option :selected="item.key === filter.defect" :value="item.key">{{ item.value }}</option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="form-group col-md-6">
                            <label for="comment">Коментар</label>
                            <input name="comment" type="text" class="form-control" v-model="filter.comment">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="order_number">№ Замовлення</label>
                            <input name="order_number" type="text" class="form-control" v-model="filter.order_number">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="form-group col-md-6">
                            <label for="remainder">Залишок</label>
                            <input type="checkbox" id="remainder" name="deprecated" style="margin-left: 5px;" v-model="filter.remainder">
                        </div>
                    </div>
                </div>
                <div class="modal-body" v-if="isLoading">
                    <div  class="d-flex justify-content-center mt-2">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div v-if="!isLoading" class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" @click="initFilter">Фильтровать</button>
                    <button type="submit" class="btn btn-secondary" @click="clearFilter">Очистить</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent, PropType} from "vue";
import {FilterType} from "./Types/FilterType";
import axios from "axios";
import {Option, OptionGoods} from "../../../../common/Types/Option";

export default defineComponent({
    name: "Filter",
    props: {
        filterParams: {
            type: Object as PropType<FilterType>,
            required: true,
        }
    },
    data() {
        return {
            isLoading: false,
            filter: {
                order_date_from: '',
                order_date_to: '',
                vendor_code: '',
                goods_name: '',
                goods_id: '',
                vendor_code_value: '',
                goods_name_value: '',
                defect: '',
                provider_start: '',
                manager: '',
                status: [],
                date_check_from: '',
                date_check_to: '',
                comment: '',
                order_number: '',
                remainder: false,
            } as FilterType,
            managersOptions: [] as Option[],
            providerOptions: [] as Option[],
            statusOptions: [] as Option[],
            defectsOptions: [] as Option[],
            vendorCodeList: [] as OptionGoods[],
            goodsNameList: [] as OptionGoods[],
            inputTimerVendorCode: 0,
            inputTimerGoodsName: 0,
        }
    },
    computed: {
        vendorCodeValueComputed: {
            get() {
                return this.filter.vendor_code_value;
            },
            set(value): void {
                this.filter.vendor_code_value = value;
                const goods = this.vendorCodeList.find((item: OptionGoods) => {
                    return item.id === value;
                });
                this.filter.goods_id = goods.id;
                this.filter.goods_name = goods.name;
                this.filter.vendor_code = goods.vendor_code;
            }
        },
        goodsNameValueComputed: {
            get() {
                return this.filter.goods_name_value;
            },
            set(value): void {
                this.filter.goods_name_value = value;
                const goods = this.goodsNameList.find((item: OptionGoods) => {
                    return item.id === value;
                });
                this.filter.goods_id = goods.id;
                this.filter.goods_name = goods.name;
                this.filter.vendor_code = goods.vendor_code;
            }
        }
    },
    methods: {
        addAll(options: Option[]): Option[] {
            options.unshift({key: 'all', value: 'Все'} as Option);
            return options;
        },
        closeButton(): void {
            this.$emit('closeButton');
        },
        initFilter(): void {
            this.$emit('initFilter', this.filter);
        },
        searchByVendorCode(event) {
            clearTimeout(this.inputTimerVendorCode);
            this.clearGoods();
            this.inputTimerVendorCode = setTimeout(() => {
                if (event.target.value !== '') {
                    axios.get('/api/v1/goods/vendor_code/' + event.target.value).then((response) => {
                        if (response.status !== 200) {
                            throw Error("Error");
                        }
                        this.vendorCodeList = response.data.data.records;
                        this.goodsNameList = [];
                    }).catch((error) => {
                        console.error(error);
                        alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
                    })
                }

            }, 500);
        },
        searchByGoodsName(event) {
            clearTimeout(this.inputTimerGoodsName);
            this.clearGoods();
            this.inputTimerGoodsName = setTimeout(() => {
                if (event.target.value !== '') {
                    axios.get('/api/v1/goods/goods_name/' + event.target.value).then((response) => {
                        if (response.status !== 200) {
                            throw Error("Error");
                        }
                        this.goodsNameList = response.data.data.records;
                        this.vendorCodeList = [];
                    }).catch((error) => {
                        console.error(error);
                        alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
                    })
                }
            }, 500);
        },
        clearGoods(): void {
            this.goodsNameList = [];
            this.vendorCodeList = [];
        },
        clearFilter(): void {
            this.filter = {
                order_date_from: '',
                order_date_to: '',
                vendor_code: '',
                goods_name: '',
                defect: '',
                provider_start: '',
                manager: '',
                status: [],
                date_check_from: '',
                date_check_to: '',
                comment: '',
                remainder: false,
            };
        },
        getFilterOptions(): void {
            const response = ['providers', 'status', 'defects', 'users'].map((item: string) => this.getByUrl(item));
            Promise.all(response).then(values => {
                this.managersOptions = this.addAll(values[0].data);
                this.defectsOptions = this.addAll(values[1].data);
                this.providerOptions = this.addAll(values[2].data);
                this.statusOptions = this.addAll(values[3].data);

                this.isLoading = false;
            }).catch((e) => {
                console.error(e);
                alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
            });
        },
        async getByUrl(param: string): Promise<Option[]> {
            return axios.get(`/api/v1/${param}/all`).then((response) => {
                if (response.status !== 200) {
                    throw Error("Error");
                }
                return response.data;
            });
        }
    },
    created() {
        this.isLoading = true;
        this.filter = this.filterParams;
        this.getFilterOptions();
    },
});
</script>

<style module>
.custom__popup {
    display: block;
    background-color: rgba(0, 0, 0, 0.5);
}
</style>
