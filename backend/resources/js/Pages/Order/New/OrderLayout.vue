<script>
import '@/../css/views/order.css'
import { isCurrentRoute } from '@/Composables/useUtils'
import HasOwnTranslations from '@/Mixins/HasOwnTranslations'
import SharedData from '@/Mixins/SharedData'
import { Head as HeadTitle } from '@inertiajs/vue2'
import { useDebounceFn } from '@vueuse/core'
import OrderOverviewFilters from '../Components/OrderOverviewFilters.vue'
import { removeNull } from '@/Composables/useFilters'
import IncomingOrderForm from '../Incoming/Components/IncomingOrderForm.vue'
import OutgoingOrderForm from '../Outgoing/Components/OutgoingOrderForm.vue'
import MenuListItem from '@/Components/MenuListItem.vue'

export default {
    components: {
        HeadTitle,
        OrderOverviewFilters,
        IncomingOrderForm,
        OutgoingOrderForm,
        MenuListItem,
    },

    mixins: [SharedData, HasOwnTranslations],

    props: {
        orderStatuses: {
            required: false,
            type: Array,
            default: () => [],
        },
        title: {
            required: false,
            type: String,
            default: 'Order',
        },
        summaries: {
            required: true,
            type: Object,
        },
        filters: {
            required: true,
            type: [Object, Array],
            default: () => ({}),
        },
        paginator: {
            required: false,
            type: Object,
            default: () => ({}),
        },
        ownedPlaces: {
            required: false,
            type: Array,
            default: () => [],
        },
        currentAddress: {
            required: false,
            type: Object,
            default: () => ({}),
        },
    },

    data: (vm) => ({
        pageFilters: {
            status: vm.filters?.status,
            noInvoice: vm.filters?.noInvoice,
            q: vm.filters?.q,
        },
        isLoading: false,
        dateRanges: {
            startDate: null,
            endDate: null,
        },
        activeTab: null,
        isShowFilters: false,
        isShowSelectActionMenu: false,
    }),

    computed: {
        pages() {
            let additional = []
            if (this.user.impersonating) {
                additional = [
                    {
                        title: this.$t('order.labels.incoming-orders'),
                        icon: 'move_to_inbox',
                        route: 'orders.incoming.index',
                        matchRoute: 'orders.incoming.*',
                        name: 'incoming',
                    },
                ]
            }

            return [
                ...additional,
                {
                    title: this.$t('order.labels.outgoing-orders'),
                    icon: 'outbox',
                    route: 'orders.outgoing.index',
                    matchRoute: 'orders.outgoing.*',
                    name: 'outgoing',
                },
            ]
        },

        hasFilters() {
            return Object.values(this.filters)
                .filter((filter) => !!filter)
                .flat().length
        },

        isImpersonating() {
            return this.$page.props?.user?.impersonating ?? false
        },

        activeRouteIsIncoming() {
            return isCurrentRoute('orders.incoming.index')
        },

        activeRouteIsOutgoing() {
            return isCurrentRoute('orders.outgoing.index')
        },

        activeRouteIsOverview() {
            return isCurrentRoute('orders.overview')
        },
    },

    watch: {
        pageFilters: {
            deep: true,
            handler(newFilters) {
                this.emitFiltersChanged(removeNull(newFilters))
            },
        },
    },

    mounted() {
        this.emitFiltersChanged = useDebounceFn((filters) => {
            this.$root.$emit('orders.filters.changed', { filters })
        }, 500)

        // Identify active tab
        const currentRoute = this.$route().current()

        if (currentRoute == 'orders.overview') {
            this.activeTab = 0
        }
        if (this.isImpersonating) {
            if (currentRoute == 'orders.incoming.index') {
                this.activeTab = 1
            }

            if (currentRoute == 'orders.outgoing.index') {
                this.activeTab = 2
            }
        } else {
            if (currentRoute == 'orders.outgoing.index') {
                this.activeTab = 1
            }
        }

        this.$root.$on('orders.filters.isLoading', this.toggleLoading)
    },

    beforeDestroy() {
        this.$root.$off('orders.filters.isLoading', this.toggleLoading)
    },

    methods: {
        toggleLoading(isLoading) {
            this.isLoading = isLoading
        },

        createButtonClicked() {
            this.isShowSelectActionMenu = !this.isShowSelectActionMenu
        },

        handleInput() {
            if (!this.isLoading) {
                this.$refs.searchField.focus()
            }
        },

        clearFilters() {
            this.$root.$emit('orders.filters.clear')
        },
    },
}
</script>
<template>
    <v-container
        fill-height
        fluid
        px-3
        align-content-start
        class="tw-pt-0 sm:tw-pt-theme-3"
    >
        <HeadTitle :title="title" />

        <IncomingOrderForm ref="incomingOrderForm" />

        <OutgoingOrderForm
            ref="outgoingOrderForm"
            :owned-places="ownedPlaces"
            :current-address="currentAddress"
        />

        <v-menu
            v-model="isShowSelectActionMenu"
            activator=".create-order-button"
            offset-y
            z-index="21"
        >
            <v-list dense>
                <MenuListItem
                    icon="move_to_inbox"
                    @click="$refs.incomingOrderForm.show()"
                >
                    {{ $t('order.incoming.buttons.create') }}
                </MenuListItem>
                <MenuListItem
                    icon="outbox"
                    @click="$refs.outgoingOrderForm.show()"
                >
                    {{ $t('order.outgoing.buttons.create') }}
                </MenuListItem>
            </v-list>
        </v-menu>
        <v-layout
            row
            wrap
        >
            <v-flex xs12>
                <v-card
                    class="order-toolbar"
                    color="primary-light"
                >
                    <v-card-text class="pb-1">
                        <v-layout
                            row
                            wrap
                            :class="{
                                'align-end': $vuetify.breakpoint.smAndUp,
                                'align-center': $vuetify.breakpoint.xs,
                            }"
                        >
                            <v-flex
                                xs10
                                sm8
                                class="tw-flex tw-items-end py-1"
                            >
                                <v-text-field
                                    ref="searchField"
                                    v-model="pageFilters.q"
                                    hide-details
                                    :placeholder="
                                        $t('order.labels.search-placeholder')
                                    "
                                    color="primary"
                                    solo
                                    flat
                                    clearable
                                    :loading="isLoading"
                                    @click:clear="clearFilters"
                                    @input="handleInput"
                                >
                                    <template #append>
                                        <v-icon
                                            color="primary"
                                            class="tw-cursor-pointer sm:tw-hidden"
                                            @click="
                                                isShowFilters = !isShowFilters
                                            "
                                        >
                                            filter_alt
                                        </v-icon>
                                    </template>
                                </v-text-field>
                            </v-flex>
                            <v-flex
                                xs2
                                sm4
                                class="tw-text-center tw-self-center"
                            >
                                <div
                                    class="tw-flex tw-flex-row tw-align-bottom tw-align-center"
                                >
                                    <v-btn
                                        color="white"
                                        large
                                        depressed
                                        class="white--text !tw-min-w-8 xs:tw-hidden"
                                        @click="isShowFilters = !isShowFilters"
                                    >
                                        <v-icon color="primary">
                                            filter_alt
                                        </v-icon>
                                    </v-btn>

                                    <v-btn
                                        :large="$vuetify.breakpoint.smAndUp"
                                        color="primary"
                                        depressed
                                        class="create-order-button white--text !tw-min-w-8 xs:!tw-h-14 xs:!tw-my-0"
                                        @click="(e) => createButtonClicked(e)"
                                    >
                                        <v-icon
                                            v-if="$vuetify.breakpoint.smAndDown"
                                        >
                                            add
                                        </v-icon>
                                        <span>
                                            <span
                                                v-if="
                                                    $vuetify.breakpoint.mdAndUp
                                                "
                                            >
                                                {{ $t('buttons.create') }}
                                            </span>
                                            <span
                                                v-if="
                                                    $vuetify.breakpoint.lgAndUp
                                                "
                                            >
                                                {{ $tc('labels.orders') }}
                                            </span>
                                        </span>
                                    </v-btn>
                                </div>
                            </v-flex>
                            <!-- Filters -->
                            <OrderOverviewFilters
                                :model-value.sync="pageFilters"
                                :is-loading="isLoading"
                                :is-show="isShowFilters"
                            />
                        </v-layout>
                    </v-card-text>
                    <v-card-actions class="px-0 pb-0 hidden-xs-only">
                        <v-tabs
                            v-model="activeTab"
                            fixed-tabs
                            grow
                            hide-slider
                            class="order-tabs tw-w-full"
                        >
                            <v-tab
                                ripple
                                active
                                class="all-tab tw-relative"
                                @click="
                                    $inertia.get($route('orders.overview'), {
                                        ...filters,
                                    })
                                "
                            >
                                <div class="mr-5">
                                    {{ $t('order.labels.show-all') }}
                                </div>
                                <div class="tw-absolute tw-right-3">
                                    <v-chip
                                        color="red accent-3"
                                        text-color="white"
                                        small
                                    >
                                        {{ summaries.all.requires_response }}
                                    </v-chip>
                                    <v-chip
                                        color="grey-lighter"
                                        text-color="grey-darker"
                                        small
                                    >
                                        {{ summaries.all.total }}
                                    </v-chip>
                                </div>
                            </v-tab>
                            <v-tab
                                v-if="isImpersonating"
                                ripple
                                class="incoming-tab tw-relative"
                                @click="
                                    $inertia.get(
                                        $route('orders.incoming.index'),
                                        {
                                            ...filters,
                                        },
                                    )
                                "
                            >
                                <div class="mr-5">
                                    {{ $t('order.labels.show-incoming') }}
                                </div>
                                <div class="tw-absolute tw-right-3">
                                    <v-chip
                                        color="red accent-3"
                                        text-color="white"
                                        small
                                    >
                                        {{
                                            summaries.incoming.requires_response
                                        }}
                                    </v-chip>
                                    <v-chip
                                        color="grey-lighter"
                                        text-color="grey-darker"
                                        small
                                    >
                                        {{ summaries.incoming.total }}
                                    </v-chip>
                                </div>
                            </v-tab>
                            <v-tab
                                ripple
                                class="outgoing-tab tw-relative"
                                @click="
                                    $inertia.get(
                                        $route('orders.outgoing.index'),
                                        {
                                            ...filters,
                                        },
                                    )
                                "
                            >
                                <div class="mr-5">
                                    {{ $t('order.labels.show-outgoing') }}
                                </div>
                                <div class="tw-absolute tw-right-3">
                                    <v-chip
                                        color="red accent-3"
                                        text-color="white"
                                        small
                                    >
                                        {{
                                            summaries.outgoing.requires_response
                                        }}
                                    </v-chip>
                                    <v-chip
                                        color="grey-lighter"
                                        text-color="grey-darker"
                                        small
                                    >
                                        {{ summaries.outgoing.total }}
                                    </v-chip>
                                </div>
                            </v-tab>
                        </v-tabs>
                    </v-card-actions>
                </v-card>
                <div class="py-4">
                    <slot />
                </div>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<style lang="scss">
.order-toolbar {
    @apply xs:tw-fixed xs:tw-inset-x-0 xs:tw-top-0 xs:tw-mt-theme-5 xs:tw-bg-primary-lighter tw-z-10;
}

.vue-daterange-picker {
    .form-control {
        border: none;
        @apply tw-p-0;

        .v-input__slot {
            &::before {
                border: 1px solid #0000006b;
            }
        }
    }
}
</style>
