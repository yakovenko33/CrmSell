<template>
    <div>
        <form tag="form" ref="myForm" >
            <div class="form-group row">
                <TextInput
                    v-model="form.numberOrder"
                    :label="'№ Замовлення'"
                    :name="'numberOrder'"
                    :errors="errors"
                />

                <SelectFiled
                    v-model="providerStartComputed"
                    :selected="providerStartComputed"
                    :label="'Постачальник (потенційний)'"
                    :name="'providerStart'"
                    :options="providerOptions"
                />
            </div>

            <div class="form-group row">
                <div class="form-group col-md-6">
                    <label for="vendorCode">Артикул</label>
                    <input name="vendorCode" class="form-control" type="text" v-model="form.vendorCode" @input="searchByVendorCode">
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
                    <input name="goodsName" class="form-control" type="text" v-model="form.goodsName" @input="searchByGoodsName">
                    <template v-if="goodsNameList.length > 0">
                        <select class="form-select" v-model="goodsNameValueComputed" size="5">
                            <template v-for="item in goodsNameList">
                                <option :value="item.id">{{ item.name }}</option>
                            </template>
                        </select>
                    </template>
                    <span v-if="'goodsId' in errors" role="alert" class="text-danger" >{{ errors.goodsId }}</span>
                </div>
            </div>

            <div class="form-group row">
                <TextAreaInput
                    v-model="form.managerComment"
                    :label="'Коментар/уточнення постачальника'"
                    :name="'managerComment'"
                    :cols="'30'"
                    :rows="'10'"
                    :errors="errors"
                />

                <NumberInput
                    v-model="form.sellPrice"
                    :label="'Ціна'"
                    :name="'sellPrice'"
                    :errors="errors"
                />
            </div>


            <div class="form-group row">
                <NumberInput
                    v-model="form.amountInOrder"
                    :label="'К-ть в замовленні'"
                    :name="'amountInOrder'"
                    :errors="errors"
                />

                <TextInput
                    v-model="form.comfyCode"
                    :label="'Код номенклатуры'"
                    :name="'comfyCode'"
                    :errors="errors"
                />
            </div>


            <div class="form-group row">
                <TextInput
                    v-model="form.comfyGoodsName"
                    :label="'Наименование продукта'"
                    :name="'comfyGoodsName'"
                    :errors="errors"
                />

                <div class="form-group col-md-6">
                    <label for="comfyBrand">Наименование бренда</label>
                    <input name="comfyBrand" class="form-control" type="text" v-model="form.comfyBrandText" @input="searchByBrandName">
                    <template v-if="brandsNameList.length > 0">
                        <select class="form-select" v-model="form.comfyBrand" size="5">
                            <template v-for="item in brandsNameList">
                                <option :value="item.key">{{ item.value }}</option>
                            </template>
                        </select>
                    </template>
                    <span v-if="'comfyBrand' in errors" role="alert" class="text-danger" >{{ errors.comfyBrand }}</span>
                </div>
            </div>


            <div class="form-group row">
                <TextInput
                    v-model="form.comfyCategory"
                    :label="'Наименование категории'"
                    :name="'comfyCategory'"
                    :errors="errors"
                />

                <NumberInput
                    v-model="form.comfyPrice"
                    :label="'Цена/значение'"
                    :name="'comfyPrice'"
                    :errors="errors"
                />
            </div>


            <div v-if="!isLoading" class="text-center mt-2">
                <button type="button" class="btn app-btn-primary" @click="onSubmit">Додати</button>
            </div>
            <div v-if="isLoading" class="d-flex justify-content-center mt-2">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </form>
    </div>
</template>

<script lang="ts">
import {defineAsyncComponent, defineComponent} from "vue";
import * as yup from "yup";
import {Option, OptionGoods} from "../../../../common/Types/Option";
import axios from "axios";
import {ProvidersEnum} from "../../../Admin/pages/Providers/enum/ProvidersEnum";

const TextInput = defineAsyncComponent(() => import('@/js/src/common/components/EditPage/Fields/TextInput.vue'));
const TextAreaInput = defineAsyncComponent(() => import('@/js/src/common/components/EditPage/Fields/TextAreaInput.vue'));
const SelectFiled = defineAsyncComponent(() => import('@/js/src/common/components/EditPage/Fields/SelectFiled.vue'));
const NumberInput = defineAsyncComponent(() => import('@/js/src/common/components/EditPage/Fields/NumberInput.vue'));

interface OptionProvider extends Option {
    type: string;
}

export default defineComponent({
    name: "CreateForm",
    components: {
        TextInput,
        TextAreaInput,
        SelectFiled,
        NumberInput,
    },
    data() {
        return {
            isLoading: false,
            vendorCodeList: [] as OptionGoods[],
            goodsNameList: [] as OptionGoods[],
            brandsNameList: [] as Option[],
            inputTimerVendorCode: 0,
            inputTimerGoodsName: 0,
            inputTimerBrandName: 0,
            form: {
                numberOrder: '',
                vendorCode: '',
                vendorCodeValue: '',
                goodsName: '',
                goodsNameValue: '',
                sellPrice: 0,
                managerComment: '',
                providerStart: '',
                amountInOrder: 0,
                comfyGoodsName: '',
                comfyCode: '',
                comfyBrand: '',
                comfyBrandText: '',
                goodsNameText: '',
                comfyCategory: '',
                comfyPrice: '',
                goodsId: '',
                providerType: '',
            },
            validation: yup.object().shape({
                numberOrder: yup.string().trim().required('Поле обзательное').max(50, 'Максимальное количество символов 50'),
                sellPrice:  yup.number()
                    .transform((value) => (isNaN(value) ? undefined : value))
                    .required('Поле обзательное')
                    .positive('Поле должно быть больше 0')
                    .test('is-decimal', 'Должно иметь два знака после запятой', (value) => {
                        return (value) ? /^\d+(\.\d{1,2})?$/.test(value.toString()) : true;
                    }),
                managerComment: yup.string().trim().max(1000, 'Максимальное количество символов 1000'),
                goodsId: yup.string().trim().required('Не был выбран коректно товар.'),
                providerStart: yup.string().trim().required('Поле обзательное'),
                amountInOrder: yup.number()
                    .transform((value) => (isNaN(value) ? undefined : value))
                    .required('Поле обзательное')
                    .positive('Поле должно быть больше 0')
                    .integer('Поле должно быть целочисельное'),
                comfyCode: yup.string().when(['providerType'], {
                    is: (providerType) => {
                        return providerType === ProvidersEnum.COMFY;
                    },
                    then: (schema) => yup.string().trim().required('Поле обзательное').max(50, 'Максимальное количество символов 50'),
                    otherwise: (schema) => yup.string().trim().max(50, 'Максимальное количество символов 50'),
                }),
                comfyGoodsName: yup.string().when(['providerType'], {
                    is: (providerType) => {
                        return providerType === ProvidersEnum.COMFY;
                    },
                    then: (schema) => yup.string().trim().required('Поле обзательное').max(150, 'Максимальное количество символов 150'),
                    otherwise: (schema) => yup.string().trim().max(150, 'Максимальное количество символов 150'),
                }),
                comfyBrand: yup.string().when(['providerType'], {
                    is: (providerType) => {
                        return providerType === ProvidersEnum.COMFY;
                    },
                    then: (schema) => yup.string().trim().required('Поле обзательное').max(50, 'Максимальное количество символов 50'),
                    otherwise: (schema) => yup.string().trim().max(50, 'Максимальное количество символов 50'),
                }),
                comfyCategory: yup.string().when(['providerType'], {
                    is: (providerType) => {
                        return providerType === ProvidersEnum.COMFY;
                    },
                    then: (schema) => yup.string().trim().required('Поле обзательное').max(100, 'Максимальное количество символов 100'),
                    otherwise: (schema) => yup.string().trim().max(100, 'Максимальное количество символов 100'),
                }),
                comfyPrice: yup.string().when(['providerType'], {
                    is: (providerType) => {
                        return providerType === ProvidersEnum.COMFY;
                    },
                    then: (schema) => yup.number().required('Поле обзательное')
                        .transform((value) => (isNaN(value) ? undefined : value))
                        .positive('Поле должно быть больше 0')
                        .test('is-decimal', 'Должно иметь два знака после запятой', (value) => {
                            return (value) ? /^\d+(\.\d{1,2})?$/.test(value.toString()) : true;
                        }),
                    otherwise: (schema) => yup.number()
                        .transform((value) => (isNaN(value) ? undefined : value))
                        .positive('Поле должно быть больше 0')
                        .test('is-decimal', 'Должно иметь два знака после запятой', (value) => {
                            return (value) ? /^\d+(\.\d{1,2})?$/.test(value.toString()) : true;
                        }),
                }),
            }),
            errors: {},
            providerOptions: [] as OptionProvider[],
        }
    },
    computed: {
        vendorCodeValueComputed: {
            get() {
                return this.form.vendorCodeValue;
            },
            set(value): void {
                this.form.vendorCodeValue = value;
                const goods = this.vendorCodeList.find((item: OptionGoods) => {
                    return item.id === value;
                });
                this.form.goodsId = goods.id;
                this.form.goodsName = goods.name;
                this.form.vendorCode = goods.vendor_code;
            }
        },
        providerStartComputed: {
            get() {
                return this.form.providerStart;
            },
            set(value): void {
                this.form.providerStart = value;
                const provider = this.providerOptions.find((item: OptionProvider) => {
                    return item.key === value;
                });
                this.form.providerType = provider.type;
            }
        },
        goodsNameValueComputed: {
            get() {
                return this.form.goodsNameValue;
            },
            set(value): void {
                this.form.goodsNameValue = value;
                const goods = this.goodsNameList.find((item: OptionGoods) => {
                    return item.id === value;
                });
                this.form.goodsId = goods.id;
                this.form.goodsName = goods.name;
                this.form.vendorCode = goods.vendor_code;
            }
        }
    },
    async created() {
        this.isLoading = true;
        try {
            await this.getProviders();
            this.isLoading = false;
        } catch (e) {
            alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
            this.isLoading = false;
        }
    },
    methods: {
        onSubmit() {
            this.errors = {};
            const schema = this.validation;
            schema.validate(this.form, { abortEarly: false })
                .then(valid => this.create())
                .catch(errors => {
                    const errorsObject = {};
                    errors.inner.forEach(err => {
                        errorsObject[err.path] = err.message;
                    });
                    this.errors = errorsObject;
                });
        },
        async getProviders(): Promise<void> {
            return axios.get('/api/v1/providers/all').then((response) => {
                if (response.status !== 200) {
                    throw Error("Error");
                }
                this.providerOptions = response.data.data;
            });
        },
        create(): void {
            this.isLoading = true;
            axios.post('/api/v1/order', this.form).then(async (response) => {
                if (response.status === 422) {
                    response.data.errors.forEach((item) => {
                        this.errors[item.field] = item.message;
                    })
                    this.isLoading = false;
                    return;
                }
                if (response.status === 201) {
                    this.$router.push({name: 'orders'});
                } else {
                    alert(response.data.errors[0]);
                    this.isLoading = false;
                }
            }).catch((error) => {
                console.error(error)
                alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
                this.isLoading = false;
            });
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
        searchByBrandName(event) {
            clearTimeout(this.inputTimerBrandName);
            this.inputTimerBrandName = setTimeout(() => {
                if (event.target.value !== '') {
                    axios.get('/api/v1/brands/' + event.target.value).then((response) => {
                        if (response.status !== 200) {
                            throw Error("Error");
                        }
                        this.brandsNameList = response.data.data.records;
                    }).catch((error) => {
                        console.error(error);
                        alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
                    })
                }
            }, 500);
        },
    }
});
</script>

<style scoped>

</style>
