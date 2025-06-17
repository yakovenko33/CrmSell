<template>
    <div>
        <form tag="form" ref="myForm">
            <div class="form-group row">
                <div class="form-group col-md-6">
                    <label for="firstName">Имя</label>
                    <input name="firstName" type="text" class="form-control" placeholder="Имя"  v-model="form.firstName">
                    <span v-if="'firstName' in errors" role="alert" class="text-danger" >{{ errors.firstName }}</span>
                </div>
                <div class="form-group col-md-6">
                    <label for="lastName">Фамилия</label>
                    <input name="lastName" type="text" class="form-control" placeholder="Фамилия"  v-model="form.lastName">
                    <span v-if="'lastName' in errors" role="alert" class="text-danger" >{{ errors.lastName }}</span>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group col-md-6">
                    <label for="email">Email</label>
                    <input name="email" type="email" class="form-control" placeholder="Email" v-model="form.email">
                    <span v-if="'email' in errors" role="alert" class="text-danger" >{{ errors.email }}</span>
                </div>

                <div class="form-group col-md-6">
                    <label for="email">Роли</label>
                    <select class="form-select" name="roles" v-model="form.roles" multiple>
                        <template v-for="role in rolesEnum">
                            <option :value="role.key">{{ role.value }}</option>
                        </template>
                    </select>
                    <span v-if="'roles' in errors" role="alert" class="text-danger" >{{ errors.roles }}</span>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group col-md-6">
                    <label for="password">Пароль</label>
                    <input name="password" type="password" ref="password" class="form-control" placeholder="Пароль" v-model="form.password">
                    <span v-if="'password' in errors" role="alert" class="text-danger" >{{ errors.password }}</span>
                </div>
                <div class="form-group col-md-6">
                    <label for="confirmPassword">Подвердите пароль</label>
                    <input name="confirmPassword" type="password" class="form-control" placeholder="Подвердите пароль" v-model="form.confirmPassword">
                    <span v-if="'confirmPassword' in errors" role="alert" class="text-danger" >{{ errors.confirmPassword }}</span>
                </div>
            </div>

            <div v-if="!isLoading" class="text-center mt-2">
                <button type="button" @click="onSubmit" class="btn app-btn-primary">Создать</button>
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


interface Options {
    key: string;
    value: string;
}

export default defineComponent({
    name: "CreateForm",
    data() {
        return {
            isLoading: false,
            form: {
                firstName: '',
                lastName: '',
                email: '',
                password: '',
                confirmPassword: '',
                roles: [] as string[],
                status: 0,
            },
            validation: yup.object().shape({
                firstName:  yup.string().required('Поле обзательное').min(2, 'Минимальное количество символов 2'),
                lastName: yup.string().required('Поле обзательное').min(2, 'Минимальное количество символов 2'),
                email:  yup.string().required('Поле обзательное').email('Некоректный формат.').min(8, 'Минимальное количество символов 8'),
                password: yup.string().required('Поле обзательное').min(8, 'Минимальное количество символов 8'),
                confirmPassword: yup.string()
                     .oneOf([yup.ref('password'), null], 'Пароли должны совпадать')
                     .required('Поле обзательное'),
                roles: yup.array().min(1, 'Выберите хотя бы одну роль'),
            }),
            rolesEnum: [] as Options[],
            errors: {},
        }
    },
    async created() {
        this.isLoading = true;
        try {
            await this.getRoles();
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
        create(): void {
            this.isLoading = true;
            axios.post('/api/v1/user', this.form).then(async (response) => {
                if (response.status === 422) {
                    response.data.errors.forEach((item) => {
                        this.errors[item.field] = item.message;
                    })
                    this.isLoading = false;
                    return;
                }
                if (response.status === 201) {
                    this.$router.push({name: 'users-list'});
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
        async getRoles(): Promise<void> {
            return axios.get('/api/v1/roles').then((response) => {
                if (response.status !== 200) {
                    throw Error("Error");
                }
                this.rolesEnum = response.data.data;
            });
        }
    }
});

</script>

<style scoped>

</style>
