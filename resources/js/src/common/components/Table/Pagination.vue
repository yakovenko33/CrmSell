<template>
    <nav class="app-pagination">
        <ul class="pagination justify-content-center">

            <li class="page-item" v-if="pagination.pages.current_page !== 1" @click="clickButton(1)">
                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">First</a>
            </li>

            <li class="page-item" v-if="pagination.pages.previous_page" @click="clickButton(pagination.pages.previous_page)">
                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
            </li>


            <li class="page-item" v-if="pagination.pages.previous_page && pagination.pages.current_page !== 1"
                @click="clickButton(pagination.pages.previous_page)"
            >
                <a class="page-link" href="#">{{  pagination.pages.previous_page }}</a>
            </li>
            <li class="page-item active"><a class="page-link" href="#">{{ pagination.pages.current_page }}</a></li>

            <li class="page-item" v-if="pagination.pages.next_page && pagination.pages.current_page !== 1"
                @click="clickButton(pagination.pages.next_page)"
            >
                <a class="page-link" href="#">{{ pagination.pages.next_page }}</a>
            </li>
            <li class="page-item" v-if="pagination.pages.second_page" @click="clickButton(2)">
                <a class="page-link" href="#">2</a>
            </li>
            <li class="page-item" v-if="pagination.pages.third_page" @click="clickButton(3)">
                <a class="page-link" href="#">3</a>
            </li>


            <li class="page-item" v-if="pagination.pages.total_pages > 1 && pagination.pages.last_page !== pagination.pages.current_page "
                @click="clickButton(pagination.pages.next_page)"
            >
                <a class="page-link" href="#">Next</a>
            </li>
            <li class="page-item" v-if="pagination.pages.total_pages > 1 && pagination.pages.last_page !== pagination.pages.current_page"
                @click="clickButton(pagination.pages.last_page)"
            >
                <a class="page-link" href="#">Last ({{ pagination.pages.last_page }})</a>
            </li>

            <li><a class="page-link disabled" href="#">records: ({{pagination.records.from}} - {{pagination.records.to}} of {{pagination.records.all}})</a></li>
        </ul>
    </nav>
</template>

<script lang="ts">

import { defineComponent, ref,  PropType } from 'vue';
import {PaginationType} from "./Type/PaginationType";

export default defineComponent({
    name: "Pagination",
    props: {
        pagination: {
            type: Object as PropType<PaginationType>,
            required: true,
        }
    },
    methods: {
        clickButton(page: number): void {
            this.$emit("clickPagination", page);
        }
    }
});

</script>

<style scoped>

</style>
