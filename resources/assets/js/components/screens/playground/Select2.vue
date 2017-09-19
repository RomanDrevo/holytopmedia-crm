<template>
    <select>
        <slot></slot>
    </select>
</template>

<script>
export default {
    name: 'select2',
    props: ['options', 'value'],
    mounted() {
        $(this.$el)
            .val(this.value)
            .select2({ data: this.options })
            .on('change',() => {
                this.$emit('input', this.value)
            });
    },
    watch: {
        value(value) {
            $(this.$el).val(value)
        },
        options(options) {
            $(this.$el).select2({ data: options })
        }
    },
    destroyed: function () {
        $(this.$el).off().select2('destroy')
    }
}
</script>
