<template>
    <ul class="v-pagination theme--light">
        <li
            v-for="item in paginationItems"
            :key="item"
        >
            <button
                :class="[
                    'v-pagination__item tw-capitalize',
                    {
                        'primary v-pagination__item--active': item == active,
                    },
                ]"
                @click="$emit('paginate', item == 'All' ? null : item)"
            >
                {{ item }}
            </button>
        </li>
    </ul>
</template>
<script>
export default {
    props: {
        items: {
            type: Array,
            required: true,
        },

        value: {
            type: [String, Number],
            required: false,
            default: null,
        },
    },

    computed: {
        paginationItems() {
            return ['All', ...this.items]
        },

        active() {
            return this.value ?? 'All'
        },
    },
}
</script>
