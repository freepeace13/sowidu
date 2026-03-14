<template>
    <div
        :class="[`badge`]"
        :style="{ backgroundColor: status.color }"
    >
        {{ status.title | uppercase }}
    </div>
</template>

<script>
    export default {
        props: {
            status: {
                type: Object,
                required: true,
            }
        }
    }
</script>

<style lang="scss" scoped>
    .badge {
        display: block;
        line-height: 48px;
        text-align: center;
        color: #fff;
        font-weight: 700 !important;
        border-radius: 2px;
    }
</style>
