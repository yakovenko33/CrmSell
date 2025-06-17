<script lang="ts">

import {defineComponent} from "vue";
import {Option} from "@/js/src/common/Types/Option";

export default defineComponent({
    name: "SelectFiled",
    props: {
        name: {
            type: String,
            required: true
        },
        selected: {
            type: String,
            default: '',
            required: true
        },
        required: {
            type: Boolean,
            default: false,
        },
        label: {
            type: String,
            default: '',
            required: true
        },
        errors: {
            required: false,
            type: Object,
            default: () => ({}),
        },
        options: {
            type: Array,
            default: [] as Option[],
            required: true
        },
    },
    data() {
        return {
            selectedInternal: '',
            open: false,
        };
    },
    computed: {
        requiredComputed(): boolean {
            return this.required;
        },
        errorsComputed() {
            return this.errors;
        },
        computedOptions(): Option[] {
            return this.options;
        },
        computedValue: {
            get(): string {
                return this.selectedInternal || this.selected;
            },
            set(value: string): void {
                this.selectedInternal = value;
                this.$emit('update:modelValue', value);
            },
        },
    },
});
</script>

<template>
    <div class="form-group col-md-6">
        <label for="providerStart">{{ label }}</label>
        <select class="form-select" name="providerStart" v-model="computedValue">
            <template v-for="item in computedOptions">
                <option :value="item.key">{{ item.value }}</option>
            </template>
        </select>
        <span v-if="name in errors" role="alert" class="text-danger" >{{ errors[name] }}</span>
    </div>
</template>

<style scoped>

</style>
