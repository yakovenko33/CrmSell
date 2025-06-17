
<template>
    <div>
        <div :class="$style.container" style="">
            <span v-if="edit === false">{{ value }}</span>
            <EditIcon v-if="edit === false" @click="() => edit = true"/>
        </div>

        <SaveIcon v-if="edit === true" @click="save()"/>
        <CancelIcon v-if="edit === true" @click="cancel()"/>

        <div v-if="edit" class="form-group row">
            <div class="form-group">
                <input name="comfyBrand" class="form-control" type="text" v-model="comfyBrandText" @input="searchByBrandName">
                <template v-if="brandsNameList.length > 0">
                    <select class="form-select" v-model="form.value" size="5">
                        <template v-for="item in brandsNameList">
                            <option :value="item.key">{{ item.value }}</option>
                        </template>
                    </select>
                </template>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {defineAsyncComponent, defineComponent} from "vue";
import axios from "axios";
import {Option} from "../../../../../../common/Types/Option";
import {InlineOptionEdit} from "./Types/InlineEdit";
import {$http, ServerResponseId} from "../../../../../../api/$http";
import {FormType} from "./Types/FormType";
import {ResponseStatusEnum} from "../../../../../../api/enum/ResponseStatusEnum";

const EditIcon = defineAsyncComponent(() => import("@/js/src/common/components/icons/OrdersTableIcons/EditIcon.vue"));
const SaveIcon = defineAsyncComponent(() => import("@/js/src/common/components/icons/OrdersTableIcons/SaveIcon.vue"));
const CancelIcon = defineAsyncComponent(() => import("@/js/src/common/components/icons/OrdersTableIcons/CancelIcon.vue"));

export default defineComponent({
    name: "ComfyBrandEdit",
    props: {
        value: {
            type: String,
            required: true,
        },
        selected: {
            type: String,
            required: true,
        },
        entityId: {
            type: String,
            required: true,
        },
    },
    components: {
        EditIcon,
        SaveIcon,
        CancelIcon,
    },
    data() {
        return {
            isLoading: false,
            edit: false,
            brandsNameList: [],
            comfyBrandText: '',
            form: {
                entityId: '',
                field: 'comfy_brand',
                value: '',
            } as FormType,
            providerOptions: [] as Option[],
        }
    },
    async created() {
        this.form.value = this.selected;
        this.form.entityId = this.entityId;
    },
    methods: {
        cancel(): void {
            this.edit = false;
        },
        async save() {
            this.update();
        },
        update(): void {
            this.isLoading = true;
            $http.patch<FormType, ServerResponseId>('order', this.form)
                .then((response) => {
                    if (response.status !== ResponseStatusEnum.STATUS_OK) {
                        alert(response.errors[0]);
                        this.isLoading = false;
                        return;
                    }
                    this.edit = false;
                    const item = this.brandsNameList.find(item => item.key === this.form.value);
                    this.$emit('update', {
                        value: String(this.form.value),
                        label: item.value,
                        entityId: String(this.form.entityId),
                        field: this.form.field,
                    } as InlineOptionEdit);
                }).catch((error) => {
                    console.error(error);
                    alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
                    this.isLoading = false;
                });
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

<style module>
.container {
    display: flex;
    align-items: center;
}
.edit__icon {
    margin-left: 4px;
    flex: 0 0 auto;
    margin-right: 10px;
    cursor: pointer;
}
</style>
