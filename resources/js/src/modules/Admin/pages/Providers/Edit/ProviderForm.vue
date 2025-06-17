<template>
    <div>
        <form tag="form" ref="myForm">
            <div class="form-group row">
                <div class="form-group col-md-6">
                    <label for="firstName">Название</label>
                    <input name="name" type="text" class="form-control" placeholder="Название"  v-model="form.name">
                    <span v-if="'name' in errors" role="alert" class="text-danger" >{{ errors.name }}</span>
                </div>
            </div>

            <div v-if="!isLoading" class="text-center mt-2">
                <button type="button" class="btn app-btn-primary" @click="onSubmit">{{recordId === '' ? 'Создать' : 'Редактировать'}}</button>
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

export default defineComponent({
    name: "ProviderForm",
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
            },
            validation: yup.object().shape({
                name:  yup.string().required('Поле обзательное').min(2, 'Минимальное количество символов 2'),
            }),
            errors: {},
        }
    },
    created() {
        if (this.recordId !== '') {
            this.getStatus();
        }
    },
    methods: {
        getStatus(): void {
            axios.get('/api/v1/provider/' + this.recordId).then((response) => {
                if (response.status === 200) {
                    const provider = response.data.data.provider;
                    this.form.name = provider.name;
                }
            }).catch((error) => {
                console.error(error);
                alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
            })
        },
        onSubmit(): void {
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
            axios.post('/api/v1/provider', this.form).then(async (response) => {
                this.responseHandle(response, 201);
            }).catch((error) => {
                this.errorHandle(error);
            });
        },
        update(): void {
            this.isLoading = true;
            this.form.id = this.recordId;
            axios.put('/api/v1/provider', this.form).then(async (response) => {
                this.responseHandle(response,200);
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
                this.$router.push({name: 'providers'});
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
