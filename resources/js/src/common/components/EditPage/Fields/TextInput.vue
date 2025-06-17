
<script lang="ts">

import {defineComponent} from "vue";

export default defineComponent({
    name: "TextInput",
    props: {
        name: {
            type: String,
            required: true
        },
        modelValue: {
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
            type: Object,
            required: false,
            default: () => ({}),
        },
    },
    computed: {
        requiredComputed(): boolean {
            return this.required;
        },
        errorsComputed() {
            return this.errors;
        },
        valueComputed: {
            get(): string {
                return this.modelValue;
            },
            set(value: string): void {
                this.$emit('update:modelValue', value);
            },
        },
    },
});
</script>

<template>
    <div class="form-group col-md-6">
        <label :for="name">{{ label }}</label>
        <input :name="name" type="text" class="form-control" v-model="valueComputed">
        <span v-if="name in errorsComputed" role="alert" class="text-danger">{{ errorsComputed[name] }}</span>
    </div>
</template>

<style scoped>

</style>
