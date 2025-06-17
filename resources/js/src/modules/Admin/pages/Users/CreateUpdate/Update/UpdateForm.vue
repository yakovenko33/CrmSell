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
                    <input name="switchResetPassword" class="form-check-input" type="checkbox" id="switch-reset-password" :value="true" :unchecked-value="false" v-model="form.switchResetPassword"></input>
                    <label class="form-check-label" for="flexSwitchCheckDefault">Сбросить пароль пользователю</label>
                </div>
            </div>


            <div class="form-group row" v-if="form.switchResetPassword">
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

            <div class="form-group row" v-if="hasRoles([rolesEnum.ADMIN])">
                <div class="form-group col-md-6">
                    <label for="email">Статус</label>
                    <ErrorMessage name="roles" class="text-danger" />
                    <select class="form-select" name="status" v-model="form.status" >
                        <template v-for="status in statusEnum">
                            <option :value="status.key">{{ status.value }}</option>
                        </template>
                    </select>
                </div>
            </div>

            <div v-if="!isLoading" class="text-center mt-2">
                <button type="button" @click="onSubmit" class="btn app-btn-primary">Редактировать</button>
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

import permissionMixin from "../../../../../Auth/mixin/permissionMixin";
import {defineComponent} from "vue";
import * as yup from "yup";
import axios from "axios";

export default defineComponent({
    name: "UpdateForm",
    mixins: [permissionMixin],
    props: {
        userId: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            isLoading: false,
            form: {
                switchResetPassword: false,
                entityId: '',
                firstName: '',
                lastName: '',
                email: '',
                password: '',
                confirmPassword: '',
                status: 0,
            },
            statusEnum: [
                {key: '0', value: 'Активный'},
                {key: '1', value: 'Не активный'}
            ],
            validation: yup.object().shape({
                switchResetPassword: yup.boolean(),
                firstName:  yup.string().required('Поле обзательное').min(2, 'Минимальное количество символов 2'),
                lastName: yup.string().required('Поле обзательное').min(2, 'Минимальное количество символов 2'),
                email:  yup.string().required('Поле обзательное').email('Некоректный формат.').min(8, 'Минимальное количество символов 8'),
                password: yup.string()
                    .when(['switchResetPassword'], {
                        is: (switchResetPassword) => switchResetPassword,
                        then: (schema) => yup.string().required('Поле обзательное').min(8, 'Минимальное количество символов 8'),
                        otherwise: (schema) => yup.string(),
                }),
                confirmPassword: yup.string()
                    .when(['switchResetPassword'], {
                        is: (switchResetPassword) => switchResetPassword,
                        then: (schema) => yup.string()
                            .required('Поле обзательное')
                            .oneOf([yup.ref('password'), null], 'Пароли должны совпадать'),
                        otherwise: (schema) => yup.string(),
                    }),
            }),
            errors: {},
        }
    },
    created() {
        this.getUser()
    },
    methods: {
        getUser(): void {
            axios.get('/api/v1/user/' + this.userId).then((response) => {
                if (response.status === 200) {
                    const user = response.data.data.user;
                    this.form.entityId = this.userId;
                    this.form.firstName = user.first_name;
                    this.form.lastName = user.last_name;
                    this.form.email = user.email;
                    this.form.status = user.status;
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
                .then(valid => this.update())
                .catch(errors => {
                    const errorsObject = {};
                    errors.inner.forEach(err => {
                        errorsObject[err.path] = err.message;
                    });
                    this.errors = errorsObject;
                });
        },
        update(): void {
            this.isLoading = true;
            axios.put('/api/v1/user', this.form).then(async (response) => {
                if (response.status === 422) {
                    response.data.errors.forEach((item) => {
                        this.errors[item.field] = item.message;
                    })
                    this.isLoading = false;
                    return;
                }
                if (response.status === 200) {
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
    }
});

</script>

<style scoped>

</style>
