<script>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import OrderLayout from './New/OrderLayout.vue'
import EmptyOrderCard from './Components/EmptyOrderCard.vue'
import { useInfiniteScroll } from '@vueuse/core'
import OrderCard from './Components/OrderCard.vue'
import OrderCardSkeleton from './Components/OrderCardSkeleton.vue'

export default {
    components: { EmptyOrderCard, OrderCard, OrderCardSkeleton },

    layout: [AuthLayout, OrderLayout],

    props: {
        orders: {
            required: true,
            type: Array,
        },
        paginator: {
            required: true,
            type: Object,
        },
    },

    data: (vm) => ({
        items: [],
        initialUrl: vm.$page.url,
        isLoading: false,
    }),

    watch: {
        orders() {
            if (this.paginator.current_page === 1) {
                this.items = []
                this.items = this.orders
            }
        },
    },

    mounted() {
        this.items = this.orders

        this.$root.$on('orders.filters.changed', this.fetch)

        useInfiniteScroll(
            window,
            () => {
                if (!this.paginator.has_more_pages) return
                const params = new URL(this.paginator.next_page_url)

                let filters = {}

                for (const [key, value] of params.searchParams.entries()) {
                    filters[key] = value
                }
                this.fetch({
                    page: this.paginator.next_page,
                    filters,
                })
            },
            { distance: 10 },
        )
    },

    beforeDestroy() {
        this.$root.$off('orders.filters.changed', this.fetch)
    },

    methods: {
        fetch({ page = 1, filters = {} }) {
            if (page === 1) {
                this.items = []
            }

            this.$inertia.reload({
                only: ['orders', 'paginator', 'summaries', 'filters'],
                data: {
                    page,
                    ...filters,
                },
                onSuccess: ({ props: { orders } }) => {
                    this.items = [...this.items, ...orders].filter(
                        (item, index, self) =>
                            index === self.findIndex((i) => i.id === item.id),
                    )
                    window.history.replaceState(
                        {},
                        this.$page.title,
                        this.initialUrl,
                    )
                },
                onStart: () => {
                    this.$root.$emit('orders.filters.isLoading', true)
                    this.isLoading = true
                },
                onFinish: () => {
                    this.isLoading = false
                    this.$root.$emit('orders.filters.isLoading', false)
                },
            })
        },
    },
}
</script>
<template>
    <div class="w-full xs:tw-pt-[55px] mt-1">
        <v-layout
            row
            wrap
            class="tw-gap-y-4"
        >
            <v-flex
                v-if="!orders.length"
                xs12
            >
                <EmptyOrderCard />
            </v-flex>
            <v-flex
                v-for="order in items"
                :key="order.id"
                xs12
            >
                <OrderCard
                    :order="order"
                    @click:card="
                        () => $inertia.get($route('orders.show', { order }))
                    "
                />
            </v-flex>
            <v-flex
                v-for="(el, id) in 3"
                v-show="isLoading"
                :key="`skeleton-loader-${id}`"
                xs12
            >
                <OrderCardSkeleton />
            </v-flex>
        </v-layout>
    </div>
</template>
