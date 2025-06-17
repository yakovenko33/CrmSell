<template>
    <body class="app">
    <Header/>
    <div class="app-wrapper">
        <div class="app-content pt-3 p-md-3 p-lg-4">
            <div class="container-xl">
                <div class="row g-3 mb-4 align-items-center justify-content-between">
                    <div class="col-auto">
                        <h1 class="app-page-title mb-0">{{ getHead() }}</h1>
                    </div>
                </div>

                <StatusForm
                    :recordId="recordId"
                    :type="type"
                />
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
const Footer = defineAsyncComponent(() => import('@/js/src/common/components/Footer/Footer.vue'));
const StatusForm = defineAsyncComponent(() => import('@/js/src/modules/Admin/pages/Status/Edit/StatusForm.vue'));

export default defineComponent({
    name: "StatusEdit",
    components: {
        Header,
        Footer,
        StatusForm
    },
    data() {
        return {
            recordId: this.$route.params?.recordId ?? '',
            type: this.$route.params?.type ?? '',
        }
    },
    methods: {
        getHead(): string {
            return `${this.recordId === '' ? `Новий` : 'Редагувати'} статус ${StatusEnum.STATUS as string === this.type ? `замовлення` : `повернення`}`;
        },
    }
});
</script>

<style scoped>

</style>
