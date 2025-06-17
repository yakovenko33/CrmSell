<template>
    <div class="item border-bottom py-3">
        <div class="row justify-content-between align-items-center">
            <div class="col-auto">
                <div class="item-label"><strong>Роль</strong></div>
                <div class="item-data">
                    {{ roleName }}
                </div>
            </div>
            <div class="col text-end" v-if="permission">
                <a href="" class="btn-sm app-btn-secondary" @click.prevent="remove()">Удалить</a>
            </div>
        </div>
    </div>
</template>

<script lang="ts">

import {defineComponent, PropType} from "vue";

export default defineComponent({
    name: "RoleField",
    props: {
        roleName: {
            type: String,
            required: true,
        },
        roleId: {
            type: String,
            required: true,
        },
        permission: {
            type: Boolean,
            required: true,
            default: false
        }
    },
    methods: {
        remove(): void {
            if (confirm("Вы Уверены что хотите отвязать роль у пользователя?")) {
                this.$emit("removeRole", this.roleId);
            }
        }
    }
});
</script>

<style scoped>

</style>
