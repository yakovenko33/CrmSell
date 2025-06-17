<template>
    <div>
        <form tag="form" ref="myForm">
            <div class="form-group row">
                <div class="form-group col-md-6">
                    <label for="firstName">Название</label>
                    <input name="name" type="text" class="form-control" placeholder="Название"  v-model="form.name">
                    <span v-if="'name' in errors" role="alert" class="text-danger" >{{ errors.name }}</span>
                </div>
                <div v-show="recordId === ''" class="form-group col-md-6" >
                    <label for="alias">Alias</label>
                    <input name="alias" type="text" class="form-control" placeholder="Alias"  v-model="form.alias">
                    <span v-if="'alias' in errors" role="alert" class="text-danger" >{{ errors.alias }}</span>
                </div>
            </div>

            <div class="modal-body">
                <div class="form-group row">
                    <div class="form-group col-md-6">
                        <label for="email"><strong>Тип</strong></label>
                        <select class="form-select" name="type" v-model="form.type" >
                            <template v-for="type in typeOptions">
                                <option :value="type.key">{{ type.value }}</option>
                            </template>
                        </select>
                        <span v-if="'type' in errors" role="alert" class="text-danger" >{{ errors.name }}</span>
                    </div>
                </div>
            </div>

            <div v-if="!isLoading" class="text-center mt-2">
                <button type="button" class="btn app-btn-primary" @click="onSubmit"> {{recordId === '' ? 'Создать' : 'Редактировать'}}</button>
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

import {defineComponent} from "vue";
import * as yup from "yup";
import axios from "axios";
import {StatusEnum} from "../enum/StatusEnum";

export default defineComponent({
    name: "StatusForm",
    props: {
        recordId: {
            type: String,
            required: true,
        },
        type: {
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
                alias: '',
                type: '',
            },
            typeOptions: [
                {key: StatusEnum.DEFECT, value: "Повернення"},
                {key: StatusEnum.STATUS, value: "Замовлення"}
            ],
            validation: yup.object().shape({
                name:  yup.string().required('Поле обзательное').min(2, 'Минимальное количество символов 2'),
                alias: yup.string().required('Поле обзательное').min(2, 'Минимальное количество символов 2'),
                type: yup.string().required('Поле обзательное'),
            }),
            errors: {},
        }
    },
    created() {
        console.log(this.type);
        if (this.recordId !== '') {
            this.getStatus();
        }
        this.form.type = this.type
    },
    methods: {
        getStatus(): void {
            axios.get(`/api/v1/status/${this.type}/${this.recordId}`).then((response) => {
                if (response.status === 200) {
                    const status = response.data.data.status;
                    this.form.name = status.name;
                    this.form.alias = status.alias;
                    this.form.type = status.type;
                }
            }).catch((error) => {
                console.error(error);
                alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
            })
        },
        onSubmit(e): void {
            e.preventDefault();
            this.errors = {};
            const schema = this.validation;
            schema.validate(this.form, { abortEarly: false })
                .then(valid => {
                    this.recordId === '' ? this.create() : this.update();
                }).catch(errors => {
                    const errorsObject = {};
                    errors.inner.forEach(err => {
                        errorsObject[err.path] = err.message;
                    });
                    this.errors = errorsObject;
                });
        },
        create(): void {
            this.isLoading = true;
            axios.post('/api/v1/status', this.form).then(async (response) => {
                this.responseHandle(response, 201);
            }).catch((error) => {
                this.errorHandle(error);
            });
        },
        update(): void {
            this.isLoading = true;
            this.form.id = this.recordId;
            axios.put('/api/v1/status', this.form).then(async (response) => {
                this.responseHandle(response, 200);
            }).catch((error) => {
                this.errorHandle(error);
            });
        },
        errorHandle(error): void {
            alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
            console.error(error);
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
                this.$router.push({path: `/status/${this.form.type}`});
                return;
            }
            alert(response.data.errors[0]);
            this.isLoading = false;
        }
    }
});

</script>

<style scoped>

</style>
