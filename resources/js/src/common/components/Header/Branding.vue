<template>
    <div class="app-branding">
        <router-link class="app-logo" to="/">
            <img class="logo-icon me-2" src="@/assets/images/app-logo.svg" alt="logo">
            <span class="logo-text">PORTAL</span>
        </router-link>
    </div>
</template>

<script lang="ts">

import {defineComponent} from "vue";

export default defineComponent({
    name: "Branding"
});

</script>

<style scoped>

</style>
