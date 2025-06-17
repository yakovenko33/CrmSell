<template>
    <body class="app">
    <Header/>
    <div class="app-wrapper">
        <div class="app-content pt-3 p-md-3 p-lg-4">
            <div class="container-xl">
                <div class="row g-3 mb-4 align-items-center justify-content-between">
                    <div class="col-auto">
                        <h1 class="app-page-title mb-0">Статуси повернень {{ getHead() }}</h1>
                    </div>

                    <div class="col-auto">
                        <div class="page-utilities">
                            <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                                <div class="col-auto">
                                    <router-link class="btn app-btn-secondary" :to="`/status/${type}/create`">
                                        Создать
                                    </router-link>
                                </div>
                            </div><!--//row-->
                        </div><!--//table-utilities-->
                    </div><!--//col-auto-->

                </div><!--//row-->

                <StatusTable :type="type"/>
            </div>
        </div>
    </div>

    <Footer/>
    </body>
</template>

<script lang="ts">
import {defineAsyncComponent, defineComponent} from "vue";
import {StatusEnum} from "../enum/StatusEnum";

const Header = defineAsyncComponent(() => import('@/js/src/common/components/Header/Header.vue'));
const StatusTable = defineAsyncComponent(() => import('@/js/src/modules/Admin/pages/Status/List/StatusTable.vue'));

export default defineComponent({
    name: "StatusList",
    components: {
        Header,
        StatusTable,
    },
    data() {
        return {
            type: this.$route.params.type,
        }
    },
    methods: {
       getHead(): string {
           return StatusEnum.STATUS as string === this.type ? `замовлення` : `повернень`;
       }
    }
});
</script>

<style scoped>

</style>
