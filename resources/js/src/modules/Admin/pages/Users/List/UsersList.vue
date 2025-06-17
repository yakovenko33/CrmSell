<template>
    <body class="app">
    <Header/>
    <div class="app-wrapper">
        <div class="app-content pt-3 p-md-3 p-lg-4">
            <div class="container-xl">
                <div class="row g-3 mb-4 align-items-center justify-content-between">
                    <div class="col-auto">
                        <h1 class="app-page-title mb-0">Пользователи</h1>
                    </div>

                    <div class="col-auto">
                        <div class="page-utilities">
                            <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                                <div class="col-auto">
                                    <router-link class="btn app-btn-secondary" to="/user/create">
                                        Создать
                                    </router-link>
                                </div>
                                <div class="col-auto">
                                    <a class="btn app-btn-secondary" href="#">
                                        Фильтр
                                    </a>
                                </div>
                                <div class="col-auto">
                                    <a class="btn app-btn-secondary" href="#">
                                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-download me-1" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                            <path fill-rule="evenodd" d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                                        </svg>
                                        Download CSV
                                    </a>
                                </div>
                            </div><!--//row-->
                        </div><!--//table-utilities-->
                    </div><!--//col-auto-->

                </div><!--//row-->

                <UsersTableList/>
            </div>
        </div>
    </div>

    <Footer/>
    </body>
</template>

<script lang="ts">

import {defineAsyncComponent, defineComponent} from "vue";
const Header = defineAsyncComponent(() => import('@/js/src/common/components/Header/Header.vue'));
const UsersTableList = defineAsyncComponent(() => import('@/js/src/modules/Admin/pages/Users/List/UsersTableList.vue'));

export default defineComponent({
    name: "UsersList",
    components: {
        Header,
        UsersTableList
    },
});
</script>

<style scoped>

</style>
