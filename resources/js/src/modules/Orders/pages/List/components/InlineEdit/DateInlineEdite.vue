<template>
    <div>
        <div :class="$style.container" style="">
            <span v-if="edit === false">{{ getLocalDate(value) }}</span>
            <EditIcon v-if="edit === false" @click="() => edit = true"/>
        </div>

        <SaveIcon @click="save()" v-if="edit === true"/>
        <CancelIcon v-if="edit === true" @click="cancel()"/>

        <template v-if="edit">
            <div class="form-group row">
                <div class="form-group">
                    <input name="value" type="date" class="form-control" v-model="form.value">
                    <span v-if="'value' in validation" role="alert" class="text-danger" >{{ validation['value'] }}</span>
                </div>
            </div>
        </template>
    </div>
</template>

<script lang="ts">
import {defineAsyncComponent, defineComponent} from "vue";
import * as yup from "yup";
import {InlineEdit} from "./Types/InlineEdit";
import {getLocalDate} from "../../../../../../common/helpers/DateTime";
import {$http, ServerResponseId} from "../../../../../../api/$http";
import {FormType} from "./Types/FormType";
import {ResponseStatusEnum} from "../../../../../../api/enum/ResponseStatusEnum";

const EditIcon = defineAsyncComponent(() => import("@/js/src/common/components/icons/OrdersTableIcons/EditIcon.vue"));
const SaveIcon = defineAsyncComponent(() => import("@/js/src/common/components/icons/OrdersTableIcons/SaveIcon.vue"));
const CancelIcon = defineAsyncComponent(() => import("@/js/src/common/components/icons/OrdersTableIcons/CancelIcon.vue"));

export default defineComponent({
    name: "DateInlineEdite",
    props: {
        value: {
            type: String,
            required: true,
        },
        field: {
            type: String,
            required: true,
        },
        entityId: {
            type: String,
            required: true,
        },
        urlEdit: {
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
                field: '',
                value: '',
            } as FormType,
            validation: {} as Record<string, string>
        }
    },
    created() {
        this.form.value = this.value;
        this.form.field = this.field;
        this.form.entityId = this.entityId;
    },
    methods: {
        getLocalDate(date: string): string {
            return getLocalDate(date);
        },
        cancel(): void {
            this.edit = false;
        },
        async save() {
            const schema = yup.object().shape({
                value: yup.string().required('Поле обзательное')
            });
            schema.validate(this.form, { abortEarly: false })
                .then(valid => this.update())
                .catch(errors => {
                    const errorsObject = {};
                    errors.inner.forEach(err => {
                        errorsObject[err.path] = err.message;
                    });
                    this.validation = errorsObject;
                });
        },
        async update(): Promise<void> {
            this.isLoading = true;
            $http.patch<FormType, ServerResponseId>(this.urlEdit, this.form)
                .then((response) => {
                    console.log(response);
                    if (response.status === ResponseStatusEnum.VALIDATE_ERROR) {
                        response.errors.forEach((item) => {
                            this.validation[item.field] = item.message;
                        })
                        this.isLoading = false;
                        return;
                    }
                    if (response.status !== ResponseStatusEnum.STATUS_OK) {
                        alert(response.errors[0]);
                        this.isLoading = false;
                        return;
                    }
                    this.edit = false;
                    this.$emit('update', {
                        value: String(this.form.value),
                        entityId: String(this.form.entityId),
                        field: this.form.field,
                    } as InlineEdit);
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
