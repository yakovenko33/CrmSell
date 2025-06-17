<template>
    <div>
        <Form tag="form" ref="myForm" >
            <div class="form-group row">
                <div class="form-group col-md-6">
                    <label for="name">Название</label>
                    <input name="name" type="name" class="form-control" v-model="form.name">
                    <span v-if="'name' in errors" role="alert" class="text-danger" >{{ errors.vendorCode }}</span>
                </div>

                <div class="form-group col-md-6">
                    <label for="vendorCode">Артикул</label>
                    <input name="vendorCode" type="text" class="form-control" v-model="form.vendorCode">
                    <span v-if="'vendorCode' in errors" role="alert" class="text-danger" >{{ errors.vendorCode }}</span>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group col-md-6">
                    <label for="deprecated">Актуальний</label>
                    <input type="checkbox" id="deprecated" name="deprecated" v-model="form.deprecated" @change="changeDeprecated">
                </div>
            </div>

            <div v-if="!isLoading" class="text-center mt-2" @click="onSubmit">
                <button type="button" class="btn app-btn-primary">{{recordId === '' ? 'Создать' : 'Редактировать'}}</button>
            </div>
            <div v-if="isLoading" class="d-flex justify-content-center mt-2">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </Form>
    </div>
</template>

<script lang="ts">

import {defineComponent} from "vue";
import * as yup from "yup";

import axios from "axios";

export default defineComponent({
    name: "GoodsForm",
    props: {
        recordId: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            isLoading: false,
            form: {
                id: '',
                name: '',
                vendorCode: '',
                deprecated: false
            },
            errors: {}
        }
    },
    created() {
        if (this.recordId !== '') {
            this.getStatus();
        }
    },
    methods: {
        getStatus(): void {
            axios.get('/api/v1/goods/' + this.recordId).then((response) => {
                if (response.status === 200) {
                    this.form.id = this.recordId;
                    this.form.name = response.data.data.name;
                    this.form.deprecated = response.data.data.deprecated;
                    this.form.vendorCode = response.data.data.vendor_code;
                }
            }).catch((error) => {
                console.error(error);
                alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
            })
        },
        changeDeprecated() {
            this.form.deprecated = !this.form.deprecated;
        },
        onSubmit(): void {
            this.errors = {};
            const schema = yup.object().shape({
                vendorCode: yup.string().trim()
                    .required("Поле обязательное")
                    .max(60, "Поле обязательное"),
                name: yup.string().trim()
                    .required("Поле обязательное")
                    .max(150, "Поле обязательное"),
            });
            schema.validate(this.form, { abortEarly: false })
                .then(valid => this.save())
                .catch(errors => {
                    const errorsObject = {};
                    errors.inner.forEach(err => {
                        errorsObject[err.path] = err.message;
                    });
                    this.errors = errorsObject;
                });
        },
        save() {
            if (this.recordId === '') {
                this.create();
            } else {
                this.update();
            }
        },
        create(): void {
            this.isLoading = true;
            axios.post('/api/v1/goods', this.form).then(async (response) => {
                this.responseHandle(response,201);
            }).catch((error) => {
                this.errorHandle(error);
            });
        },
        update(): void {
            this.isLoading = true;
            this.form.id = this.recordId;
            axios.put('/api/v1/goods', this.form).then(async (response) => {
                this.responseHandle(response, 200);
            }).catch((error) => {
                this.errorHandle(error);
            });
        },
        errorHandle(error): void {
            console.error(error)
            alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
            this.isLoading = false;
        },
        responseHandle(response, successStatus): void {
            if (response.status === 422) {
                response.data.errors.forEach((item) => {
                    this.errors[item.field] = item.message;
                })
                this.isLoading = false;
                return;
            }
            if (response.status === successStatus) {
                this.$router.push({name: 'goods'});
            } else {
                alert(response.data.errors[0]);
                this.isLoading = false;
            }
        }
    }
});
</script>

<style scoped>

</style>
