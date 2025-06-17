<template>
    <div class="item border-bottom py-3">
        <div class="row justify-content-between align-items-center">
            <div class="col-auto">
                <div class="item-label"><strong>{{ label }}</strong></div>
                <div class="item-data">
                    {{ data }}
                </div>
            </div>
        </div><!--//row-->
    </div><!--//item-->
</template>

<script lang="ts">
import {defineComponent, PropType} from "vue";

export default defineComponent({
    name: "DetailField",
    props: {
        label: {
            type: String,
            required: true,
        },
        data: {
            type: String,
            required: true,
        }
    },
});
</script>

<style scoped>

</style>
