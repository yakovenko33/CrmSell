<template>
    <div :class="[$style.custom__popup, 'modal fade show']" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Добавити прихід</h5>
                    <button v-if="!isLoading" type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeButton">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                            <div class="form-group row">
                                <div class="form-group col-md-6">
                                    <label for="amount">К-ть забранних</label>
                                    <input name="amount" type="number" class="form-control" placeholder="Название"  v-model="form.amount">
                                    <span v-if="'amount' in errors" role="alert" class="text-danger" >{{ errors.amount }}</span>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="shipmentDate">Дата відвантаження</label>
                                    <input name="shipmentDate" type="date" class="form-control" placeholder="Название"  v-model="form.shipmentDate">
                                    <span v-if="'shipmentDate' in errors" role="alert" class="text-danger" >{{ errors.shipmentDate }}</span>
                                </div>
                            </div>
                    </div>
                </div>
                <div v-if="!isLoading"  class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeButton">Отмена</button>
                    <button type="button" class="btn btn-primary" @click="onSubmit">Добавить</button>
                </div>
                <div v-if="isLoading" class="d-flex justify-content-center mt-2">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import * as yup from "yup";
import axios from "axios";

export default defineComponent({
    name: "AddShipments",
    props: {
        orderId: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            isLoading: false,
            form: {
                amount: '',
                shipmentDate: ''
            },
            validation: yup.object().shape({
                amount: yup.number()
                    .transform((value) => (isNaN(value) ? undefined : value))
                    .required('Поле обзательное').positive('Поле должно быть больше 0').integer('Поле должно быть целочисельное'),
                shipmentDate: yup.string().trim().required('Поле обзательное')
            }),
            errors: {}
        }
    },
    methods: {
        closeButton(): void {
            this.$emit('closeButton');
        },
        onSubmit() {
            this.errors = {};
            const schema = this.validation;
            schema.validate(this.form, { abortEarly: false })
                .then(valid => this.add())
                .catch(errors => {
                    const errorsObject = {};
                    errors.inner.forEach(err => {
                        errorsObject[err.path] = err.message;
                    });
                    this.errors = errorsObject;
                });
        },
        add(): void {
            this.isLoading = true;
            axios.post('/api/v1/shipment', {
                orderId: this.orderId,
                amount: this.form.amount,
                shipmentDate: this.form.shipmentDate
            }).then((response) => {
                if (response.data.errors.length === 0) {
                    this.$emit('addShipments');
                    return;
                }
                alert(response.data.errors[0]);
                this.isLoading = false;
            }).catch((error) => {
                console.error(error)
                alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
                this.isLoading = false;
            });
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
