<template>
    <body class="app app-login p-0">
        <div class="row g-0 app-auth-wrapper">
            <div class="col-12 col-md-7 col-lg-6 auth-main-col text-center p-5">
                <div class="d-flex flex-column align-content-end">

                    <div class="app-auth-body mx-auto">
                        <div class="app-auth-branding mb-4">
                            <img class="logo-icon me-2" src="@/assets/images/app-logo.svg" alt="logo">
                        </div>
                        <h2 class="auth-heading text-center mb-5">Log in to Portal</h2>
                        <div class="auth-form-container text-start">

                            <LoginForm />

                            <div class="auth-option text-center pt-5">No Account? Sign up <a class="text-link" href="signup.html" >here</a>.</div>
                        </div>
                    </div>

                    <footer class="app-auth-footer">
                        <div class="container text-center py-3">
                            <!--/* This template is free as long as you keep the footer attribution link. If you'd like to use the template without the attribution link, you can buy the commercial license via our website: themes.3rdwavemedia.com Thank you for your support. :) */-->
                            <small class="copyright">Designed with <span class="sr-only">love</span><i class="fas fa-heart" style="color: #fb866a;"></i> by <a class="app-link" href="http://themes.3rdwavemedia.com" target="_blank">Xiaoying Riley</a> for developers</small>
                        </div>
                    </footer>
                </div>
            </div>

            <div class="col-12 col-md-5 col-lg-6 h-100 auth-background-col">
                <div class="auth-background-holder">
                </div>
                <div class="auth-background-mask"></div>
                <div class="auth-background-overlay p-3 p-lg-5">
                    <div class="d-flex flex-column align-content-end h-100">
                        <div class="h-100"></div>
                        <div class="overlay-content p-3 p-lg-4 rounded">
                            <h5 class="mb-3 overlay-title">Explore Portal Admin Template</h5>
                            <div>Portal is a free Bootstrap 5 admin dashboard template. You can download and view the template license <a href="https://themes.3rdwavemedia.com/bootstrap-templates/admin-dashboard/portal-free-bootstrap-admin-dashboard-template-for-developers/">here</a>.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</template>

<script lang="ts">

import {defineAsyncComponent, defineComponent, ref} from 'vue';
const LoginForm = defineAsyncComponent(() => import('@/js/src/pages/Login/LoginForm.vue'));

export default defineComponent({
    name: "Login",
    components: {
        LoginForm
    }
});

</script>

<style scoped>

</style>
