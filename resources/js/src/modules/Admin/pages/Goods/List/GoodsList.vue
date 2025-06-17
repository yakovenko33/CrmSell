<template>
    <body class="app">
    <Header/>
    <div class="app-wrapper">
        <div class="app-content pt-3 p-md-3 p-lg-4">
            <div class="container-xl">
                <div class="row g-3 mb-4 align-items-center justify-content-between">
                    <div class="col-auto">
                        <h1 class="app-page-title mb-0">Постачальники</h1>
                    </div>

                    <div class="col-auto">
                        <div class="page-utilities">
                            <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                                <div class="col-auto">
                                    <router-link class="btn app-btn-secondary" :to="{ name: 'goods-add' }">
                                        Создать
                                    </router-link>
                                </div>
                            </div><!--//row-->
                        </div><!--//table-utilities-->
                    </div><!--//col-auto-->

                </div><!--//row-->

                <GoodsTable/>
            </div>
        </div>
    </div>

    <Footer/>
    </body>
</template>

<script lang="ts">
import {defineAsyncComponent, defineComponent} from "vue";

const Header = defineAsyncComponent(() => import('@/js/src/common/components/Header/Header.vue'));
const GoodsTable = defineAsyncComponent(() => import('@/js/src/modules/Admin/pages/Goods/List/GoodsTable.vue'));
const Footer = defineAsyncComponent(() => import('@/js/src/common/components/Footer/Footer.vue'));

export default defineComponent({
    name: "GoodsList",
    components: {
        Header,
        GoodsTable,
        Footer
    },
});
</script>

<style scoped>

</style>
