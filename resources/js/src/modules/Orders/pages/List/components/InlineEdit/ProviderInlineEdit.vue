<template>
    <div>
        <div :class="$style.container" style="">
            <span v-if="edit === false">{{ value }}</span>
            <EditIcon v-if="edit === false" @click="() => editButton()"/>
        </div>

        <SaveIcon @click="save()" v-if="edit === true"/>
        <CancelIcon v-if="edit === true" @click="cancel()"/>

        <template v-if="edit">
            <div class="form-group row">
                <div class="form-group">
                    <select class="form-select" name="value" v-model="form.value">
                        <template v-for="item in providerOptions">
                            <option :selected="item.key === form.value" :value="item.key">{{ item.value }}</option>
                        </template>
                    </select>
                </div>
            </div>
        </template>
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
    name: "ProviderInlineEdit",
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
            form: {
                entityId: '',
                field: 'provider_start',
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
        async editButton(): Promise<void> {
            this.isLoading = true;
            try {
                await this.getStatusEnum();
                this.isLoading = false;
            } catch (e) {
                alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
                this.isLoading = false;
            }
            this.edit = true;
        },
        async getStatusEnum(): Promise<void> {
            return axios.get('/api/v1/providers/all').then((response) => {
                if (response.status !== 200) {
                    throw Error("Error");
                }
                this.providerOptions = response.data.data;
            });
        },
        async save() {
            this.update();
        },
        async update(): void {
            this.isLoading = true;
            $http.patch<FormType, ServerResponseId>('order', this.form)
                .then((response) => {
                    if (response.status !== ResponseStatusEnum.STATUS_OK) {
                        alert(response.errors[0]);
                        this.isLoading = false;
                        return;
                    }
                    this.edit = false;
                    const item = this.providerOptions.find(item => item.key === this.form.value);
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
        }
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
