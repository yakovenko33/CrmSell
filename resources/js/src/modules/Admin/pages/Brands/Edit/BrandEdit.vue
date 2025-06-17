
<template>
    <body class="app">
    <Header/>
    <div class="app-wrapper">
        <div class="app-content pt-3 p-md-3 p-lg-4">
            <div class="container-xl">
                <div class="row g-3 mb-4 align-items-center justify-content-between">
                    <div class="col-auto">
                        <h1 class="app-page-title mb-0">Новий бренд</h1>
                    </div>
                </div>

                <BrandForm :recordId="recordId"/>
            </div>
        </div>
    </div>
    <Footer/>
    </body>
</template>

<script lang="ts">

import {defineAsyncComponent, defineComponent} from "vue";

const Header = defineAsyncComponent(() => import('@/js/src/common/components/Header/Header.vue'));
const Footer = defineAsyncComponent(() => import('@/js/src/common/components/Footer/Footer.vue'));
const BrandForm = defineAsyncComponent(() => import('@/js/src/modules/Admin/pages/Brands/Edit/BrandForm.vue'));

export default defineComponent({
    name: "BrandEdit",
    components: {
        Header,
        Footer,
        BrandForm
    },
    data() {
        return {
            recordId: this.$route.params?.recordId ?? '',
        }
    }
});

</script>

<style scoped>

</style>
