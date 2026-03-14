<script>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import OrderLayout from '@/Pages/Order/OrderLayout.vue'

export default {
    layout: [AuthLayout, OrderLayout],
}
</script>
<script setup>
import OffersIndex from '@Offer/Pages/Order/Index.vue'

defineProps({
    order: {
        required: true,
        type: Object,
    },

    offers: {
        required: true,
        type: Array,
    },
})
</script>
<template>
    <OffersIndex v-bind="$props" />
</template>
