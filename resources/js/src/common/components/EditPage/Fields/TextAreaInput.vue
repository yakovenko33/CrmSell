<script lang="ts">
import {defineComponent} from "vue";

export default defineComponent({
    name: "TextAreaInput",
    props: {
        name: {
            type: String,
            required: true
        },
        required: {
            type: Boolean,
            default: false,
        },
        modelValue: {
            type: String,
            default: '',
            required: true
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
        rows: {
            type: String,
            default: '3',
        },
        cols: {
            type: String,
            default: '3',
        },
        style: {
            type: String,
            default: 'height: 5rem !important;',
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
            get(): string  {
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
        <label for="managerComment">{{ label }}</label>
        <textarea :name="name" class="form-control" :style="style" v-model="valueComputed" :cols="cols" :rows="rows"></textarea>
        <span v-if="name in errorsComputed" role="alert" class="text-danger" >{{ errorsComputed[name] }}</span>
    </div>
</template>

<style scoped>

</style>
