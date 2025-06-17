<template>
    <body class="app">
    <Header/>

    <div class="app-wrapper">
        <div class="app-content pt-3 p-md-3 p-lg-4">
            <div class="container-xl">
                <div class="row g-3 mb-4 align-items-center justify-content-between">
                    <div class="col-auto">
                        <h1 class="app-page-title mb-0">User Account</h1>
                    </div>
                </div>

                <div class="row gy-4">
                    <div class="col-12 col-lg-12">
                        <div class="app-card app-card-account shadow-sm d-flex flex-column align-items-start">
                            <div class="app-card-header p-3 border-bottom-0">
                                <div class="row align-items-center gx-3">
                                    <div class="col-auto">
                                        <div class="app-icon-holder">
                                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-person" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M10 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6 5c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                                            </svg>
                                        </div><!--//icon-holder-->
                                    </div><!--//col-->
                                    <div class="col-auto">
                                        <h4 class="app-card-title">Profile</h4>
                                    </div><!--//col-->
                                </div><!--//row-->
                            </div><!--//app-card-header-->

                            <div class="app-card-body px-4 w-100" v-if="!isLoading">
                                <DetailField
                                    :label="'Полное имя'"
                                    :data="`${user.first_name} ${user.last_name}`"
                                />

                                <DetailField
                                    :label="'Email'"
                                    :data="user.email"
                                />

                                <DetailField
                                    :label="'Status'"
                                    :data="user.status ? 'Не активный' : 'Активный'"
                                />
                            </div>

                            <div class="app-card-footer p-4 mt-auto">
                                <router-link class="btn app-btn-secondary" :to="`/user/edit/${user.id}`">
                                    Manage Profile
                                </router-link>
                            </div>
                        </div>
                    </div>

                    <RolesListPanel :userId="userId"/>
                </div>
            </div>
        </div>
    </div>

    <Footer/>
    </body>
</template>

<script lang="ts">
import {defineComponent, defineAsyncComponent} from "vue";

const Header = defineAsyncComponent(() => import('@/js/src/common/components/Header/Header.vue'));
const Footer = defineAsyncComponent(() => import('@/js/src/common/components/Footer/Footer.vue'));
const DetailField = defineAsyncComponent(() => import('./Fields/DetailField.vue'));
const RolesListPanel = defineAsyncComponent(() => import('./RolesListPanel.vue'));

import axios from "axios";


interface UserDetail {
    id: string;
    email: string;
    first_name: string;
    last_name: string;
    status: string;
}

export default defineComponent({
    name: "UserDetailView",
    components: {
        Header,
        Footer,
        DetailField,
        RolesListPanel
    },
    data() {
        return {
            isLoading: false,
            userId: this.$route.params.recordId,
            user: {
                id: '',
                email: '',
                first_name: '',
                last_name: '',
                status: '',
            } as UserDetail,
        }
    },
    created() {
        this.getUser();
    },
    methods: {
        getUser(): void {
            this.isLoading = true;
            axios.get('/api/v1/user/' + this.userId).then((response) => {
                if (response.status === 200) {
                    this.user = response.data.data.user;
                }
                this.isLoading = false;
            }).catch((error) => {
                console.error(error);
                alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
                this.isLoading = false;
            })
        },
    }
});
</script>

<style scoped>

</style>
