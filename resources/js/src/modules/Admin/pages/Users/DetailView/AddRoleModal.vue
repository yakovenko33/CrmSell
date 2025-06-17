<template>
    <div :class="[$style.custom__popup, 'modal fade show']" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Добавить роль</h5>
                    <button v-if="!isLoading" type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeButton">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="form-group col-md-6">
                            <label for="email"><strong>Роль</strong></label>
                            <select class="form-select" name="roles" v-model="role">
                                <template v-for="role in rolesEnum">
                                    <option :value="role.key">{{ role.value }}</option>
                                </template>
                            </select>
                            <span v-if="'roles' in errors" role="alert" class="text-danger" >{{ errors.roles }}</span>
                        </div>
                    </div>
                </div>
                <div v-if="!isLoading"  class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeButton">Отмена</button>
                    <button type="button" class="btn btn-primary" @click="addButton">Добавить</button>
                </div>
                <div v-if="isLoading" class="d-flex justify-content-center mt-2">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">

import {defineComponent} from "vue";
import axios from "axios";
import * as yup from "yup";

export default defineComponent({
    name: "AddRoleModal",
    props: {
        userId: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            isLoading: false,
            role: '',
            rolesEnum: [],
            validation: yup.object().shape({
                role:  yup.string().required('Поле обзательное')
            }),
            errors: {}
        }
    },
    created() {
        this.getRoles();
    },
    methods: {
        closeButton(): void {
            this.$emit('closeButton');
        },
        addButton() {
            this.errors = {};
            this.validation.validate(this.form, { abortEarly: false })
                .then(valid => this.add())
                .catch(errors => {
                    const errorsObject = {};
                    errors.inner.forEach(err => {
                        errorsObject[err.path] = err.message;
                    });
                    this.errors = errorsObject;
                });
        },
        add(): void {
            this.isLoading = true;
            axios.post('/api/v1/user/role/', {
                userId: this.userId,
                roleId: this.role
            }).then((response) => {
               if (response.status === 200) {
                   this.$emit('addRole');
                   return;
               }
               alert(response.data.errors[0]);
               this.isLoading = false;
            }).catch((error) => {
                console.error(error)
                alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
                this.isLoading = false;
            });
        },
        getRoles(): void {
            axios.get('/api/v1/roles').then((response) => {
                if (response.status !== 200) {
                    throw Error("Error");
                }
                this.rolesEnum = response.data.data;
            }).catch(error => {
                console.error(error);
                alert("Ошбка сервера, перегрузите страницу или обратитесь в тех поддержку.");
            });
        }
    }
});

</script>

<style module>

.custom__popup {
    display: block;
    background-color: rgba(0, 0, 0, 0.5);
}
</style>
