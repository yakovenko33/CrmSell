<template>
    <router-view />
</template>

<script lang="ts">

import { defineComponent } from 'vue';
import {useUserStore} from "./stores/UserStore";
import {createPinia} from "pinia";

const pinia = createPinia();
const userStore = useUserStore(pinia);

export default defineComponent({
    components: {
    },
    data() {
        return {
        };
    },
});
</script>

<style scoped>

</style>
