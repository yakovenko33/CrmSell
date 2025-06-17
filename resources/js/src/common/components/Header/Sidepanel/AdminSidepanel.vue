<template>
    <div id="app-sidepanel" :class="['app-sidepanel', mobileToggler ? 'sidepanel-visible' : '']">
        <div id="sidepanel-drop" class="sidepanel-drop"></div>
        <div class="sidepanel-inner d-flex flex-column">
            <a href="#" id="sidepanel-close" class="sidepanel-close d-xl-none" @click="sidepanelClose()">&times;</a>

            <Branding/>

            <nav id="app-nav-main" class="app-nav app-nav-main flex-grow-1">
                <ul class="app-menu list-unstyled accordion" id="menu-accordion">
                    <template v-for="item in menu">
                        <li class="nav-item has-submenu">
                            <router-link class="nav-link submenu-toggle" :to="item.to" >
                                <span class="nav-icon">
                                    <TileIcon v-if="item.icon === 'tileIcon'"/>
                                </span>
                                <span class="nav-link-text">{{ item.title }}</span>
                            </router-link>
                        </li>
                    </template>
                </ul>
            </nav>

            <FooterSidepanel/>
        </div>
    </div>
</template>

<script lang="ts">

import {StatusEnum} from "../../../../modules/Admin/pages/Status/enum/StatusEnum";

const Branding = defineAsyncComponent(() => import('@/js/src/common/components/Header/Branding.vue'));
const FooterSidepanel = defineAsyncComponent(() => import('@/js/src/common/components/Header/Sidepanel/FooterSidepanel.vue'));
const TileIcon = defineAsyncComponent(() => import('@/js/src/common/components/icons/NavIcon/TileIcon.vue'));

import {defineAsyncComponent, defineComponent, PropType} from "vue";

interface MenuItem {
    to: string;
    icon: string;
    title: string;
}

export default defineComponent({
    name: "AdminSidepanel",
    components: {
        Branding,
        FooterSidepanel,
        TileIcon
    },
    data() {
        return {
            menu: [
                {to:"/users", icon: 'tileIcon', title: 'Пользователи'},
                {to:"/providers", icon: 'tileIcon', title: 'Постачальники'},
                {to:`/status/` + StatusEnum.STATUS, icon: 'tileIcon', title: 'Статуси замовлення'},
                {to:`/status/` + StatusEnum.DEFECT, icon: 'tileIcon', title: 'Статуси повернень'},
                {to:"/goods", icon: 'tileIcon', title: 'Товари'},
                {to:"/brands", icon: 'tileIcon', title: 'Бренди'},
            ] as MenuItem[]
        }
    },
    props: {
        mobileToggler: {
            type: Boolean,
            required: true,
        }
    },
    methods: {
        sidepanelClose(): void {
            this.$emit('sidepanelClose');
        }
    }
});

</script>

<style scoped>

</style>
