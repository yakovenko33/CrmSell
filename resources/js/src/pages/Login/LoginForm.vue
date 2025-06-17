
<template>
    <form tag="form" ref="form" class="auth-form login-form">
        <div class="email mb-3 text-center">
            <label class="sr-only" for="signin-email">Email</label>
            <input name="email" type="email" class="form-control signin-email"  placeholder="Email" v-model="form.email">
            <span v-if="'email' in errors" role="alert" class="text-danger" >{{ errors.email }}</span>
            <span class="text-danger" v-if="errorServerValidation !== ''">{{ errorServerValidation }}</span>
        </div>

        <div class="password mb-3 text-center">
            <label class="sr-only" for="signin-password">Password</label>
            <input name="password" type="password" class="form-control signin-password"  placeholder="Password" v-model="form.password">
            <span v-if="'password' in errors" role="alert" class="text-danger" >{{ errors.password }}</span>

            <div class="extra mt-3 row justify-content-between">
                <div class="col-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="RememberPassword">
                        <label class="form-check-label" for="RememberPassword">
                            Remember me
                        </label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="forgot-password text-end">
                        <a href="reset-password.html">Forgot password?</a>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="!processing" class="text-center">
            <button type="submit" class="btn app-btn-primary w-100 theme-btn mx-auto" @click="onSubmit">Log In</button>
        </div>
        <button v-if="processing" class="btn btn-primary text-center" type="button" disabled>
            <span class="spinner-border spinner-border-sm" role="status"></span>
            <span class="visually-hidden">Loading...</span>
        </button>
    </form>
</template>

<script lang="ts">

import { defineComponent, ref } from 'vue';
import axios from "axios";
import { useUserStore } from "../../stores/UserStore";
import * as yup from 'yup';
import {initAuth} from "../../modules/Auth/auth.service";
const userStore = useUserStore();

export default defineComponent({
    name: "LoginForm",
    data() {
        return {
            processing: false,
            errorServerValidation: '',
            form: {
                email: '' as string,
                password: '' as string,
            },
            validation: yup.object().shape({
                email:  yup.string().required('Поле обзательное')
                    .email('Некоректный формат.')
                    .min(8, 'Минимальное количество символов 8'),
                password: yup.string()
                    .required('Поле обзательное')
                    .min(8, 'Минимальное количество символов 8'),
            }),
            errors: {},
        }
    },
    methods: {
        onSubmit(e) {
            e.preventDefault();
            this.errors = {};
            const schema = this.validation;
            schema.validate(this.form, { abortEarly: false })
                .then(valid => this.initLogin())
                .catch(errors => {
                    const errorsObject = {};
                    errors.inner.forEach(err => {
                        errorsObject[err.path] = err.message;
                    });
                    this.errors = errorsObject;
                });
        },
        async initLogin(): void {
            this.processing = true;
            this.errorServerValidation = '';
            axios.get('/sanctum/csrf-cookie').then((response) => {
                this.login();
            }).catch((error) => {
                console.error(error);
                alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
                this.processing = false;
            });
        },
        getXsrfToken(): string {
            return this.getCookie('XSRF-TOKEN');
        },
        getCookie(name) : string {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        },
        login(): void {
            axios.post('/login', this.form).then((response) => {
                if (response.status === 422) {
                    this.errorServerValidation = 'Некоректный email или пароль.'
                    this.processing = false;
                    return;
                }
                this.getUserData(this.getXsrfToken());
            }).catch((error) => {
                console.error(error)
                alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
                this.processing = false;
            });
        },
        getUserData(token: string): void {
            initAuth().init();
            userStore.login(token);
            this.$router.push({name: 'main'});
        },
    }
});

</script>

<style scoped>

</style>
