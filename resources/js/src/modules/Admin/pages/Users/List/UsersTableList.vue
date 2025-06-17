<template>
    <div class="tab-pane fade show active" id="users-all" role="tabpanel" aria-labelledby="orders-all-tab">
        <div v-if="isLoading" class="d-flex justify-content-center">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div v-if="!isLoading">
            <div class="app-card app-card-orders-table shadow-sm mb-5">
                <div class="app-card-body">
                    <div class="table-responsive">
                        <table class="table app-table-hover mb-0 text-left">
                            <HeadTable
                                :headColumns="headColumns"
                                :sortData="sortData"
                                @sort="clickSort"
                            />
                            <tbody>
                            <template v-for="item in records">
                            <tr>
                                <td class="cell">{{ item.first_name }} {{ item.last_name }}</td>
                                <td class="cell">{{ item.created_at }}</td>
                                <td class="cell">{{ item.updated_at }}</td>
                                <td class="cell">{{ item.email }}</td>
                                <td class="cell">
                                    <span :class="['badge', item.status ? 'bg-danger' : 'bg-success']">{{ item.status ? 'Не активный' : 'Активный' }}</span>
                                </td>
                                <td class="cell">
                                    <router-link class="btn-sm app-btn-secondary" :to="`/user/detail/${item.id}`">
                                        View
                                    </router-link>
                                </td>
                                <td class="cell">
                                    <router-link class="btn-sm app-btn-secondary" :to="`/user/edit/${item.id}`">
                                        Edit
                                    </router-link>
                                </td>
                            </tr>
                            </template>
                            </tbody>

                        </table>
                    </div><!--//table-responsive-->

                </div><!--//app-card-body-->
            </div><!--//app-card-->
            <Pagination
                :pagination="pagination"
                @clickPagination="refreshRecords"
            />
        </div>

    </div>
</template>

<script lang="ts">

import {defineAsyncComponent, defineComponent} from "vue";
import {SortData} from "../../../../../common/components/Table/Type/SortData";
import {HeadColumn} from "../../../../../common/components/Table/Type/HeadColumn";
import pagination from "../../../../../common/components/Table/mixins/Pagination";
import axios from "axios";
import {UserListType} from "./Type/UserListType";

const HeadTable = defineAsyncComponent(() => import("@/js/src/common/components/Table/HeadTable.vue"));
const Pagination = defineAsyncComponent(() => import('@/js/src/common/components/Table/Pagination.vue'));

export default defineComponent({
    name: "UsersTableList",
    mixins: [pagination],
    components: {
        HeadTable,
        Pagination
    },
    data() {
        return {
            isLoading: false,
            headColumns: [
                {name: 'full_name', translate: 'Полное имя', sort: true},
                {name: 'created_at', translate: 'Дата создания', sort: true},
                {name: 'updated_at', translate: 'Дата модификации', sort: true},
                {name: 'email', translate: 'Email', sort: true},
                {name: 'status', translate: 'Статус пользователя', sort: true},
                {name: 'view_detail', translate: '', sort: false},
                {name: 'view_edit', translate: '', sort: false}
            ] as HeadColumn[],
            sortData: {
                sortField: 'created_at',
                sortDir: 'desc',
            } as SortData,
            filter: {},
            records: [] as UserListType[],
        }
    },
    created() {
        this.getData();
    },
    methods: {
        getData(): void {
            this.isLoading = true;
            axios.get('/api/v1/users', {
                params: {
                    pageNumber: this.pagination.pages.current_page,
                    sortDir: this.sortData.sortDir,
                    sortField: this.sortData.sortField,
                }
            }).then((response) => {
                if (response.status === 200) {
                    const result = response.data.data;
                    this.pagination = result.pagination;
                    this.records = result.records;
                } else {
                    alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
                }
                this.isLoading = false;
            }).catch(() => {
                this.isLoading = false;
            });
        },
        clickSort(sortData: SortData) {
            this.pagination.pages.current_page = 1;
            this.sortData = sortData;
            this.getData();
        },
        refreshRecords(page: number): void {
            this.pagination.pages.current_page = page;
            this.getData();
        }
    }
})
</script>

<style scoped>

</style>
