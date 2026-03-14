<template>
    <v-navigation-drawer
        permanent
        fixed
        app
        stateless
        :mini-variant="$vuetify.breakpoint.smAndDown"
        navbar-top-margin
        mini-variant-width="60"
    >
        <v-toolbar
            flat
            class="transparent"
        >
            <v-toolbar-title class="title tw-flex tw-items-center">
                <v-icon
                    v-if="$vuetify.breakpoint.smAndDown"
                    :color="mainPage.color"
                    medium
                >
                    {{ mainPage.icon }}
                </v-icon>
                {{
                    $vuetify.breakpoint.smAndDown
                        ? ''
                        : $t('order.labels.order-types')
                }}
            </v-toolbar-title>
        </v-toolbar>
        <v-list class="pa-0">
            <v-divider />

            <template v-for="page in pages">
                <SidebarItem
                    :key="page.title"
                    :label="page.title"
                    :icon="page.icon"
                    :is-active="isCurrentActive(page)"
                    :url="$route(page.route)"
                    :badge="badges[page.name]"
                />
                <v-divider :key="`link-divider-${page.title}`" />
            </template>

            <v-subheader v-if="orderFilesLinks.length">
                {{ $t('order.labels.order-files') }}
            </v-subheader>
            <v-list-tile
                v-if="$vuetify.breakpoint.smAndDown"
                full-width
                active-class="grey lighten-4"
                pl-1
            >
                <v-list-tile-avatar class="caption">
                    {{ $t('labels.files') }}
                </v-list-tile-avatar>
            </v-list-tile>
            <template v-for="orderFilesLink in orderFilesLinks">
                <SidebarItem
                    :key="orderFilesLink.title"
                    :label="orderFilesLink.title"
                    :icon="orderFilesLink.icon"
                    :is-active="orderFilesLinksIsCurrentActive(orderFilesLink)"
                    :url="$route(orderFilesLink.route, { order: orderId })"
                    :badge="badges[orderFilesLink.name]"
                />
                <v-divider :key="`link-divider-${orderFilesLink.title}`" />
            </template>
        </v-list>
    </v-navigation-drawer>
</template>
<script>
import SidebarItem from '@components/Sidebar/SidebarItem.vue'

export default {
    components: { SidebarItem },
    props: {
        pages: {
            type: Array,
            required: true,
        },
        orderFilesLinks: {
            type: Array,
            required: false,
            default: () => [],
        },
        badges: {
            required: false,
            type: Object,
            default: () => ({ incoming: 0, outgoing: 0 }),
        },
        orderOn: {
            required: true,
            type: String,
            default: null,
        },
        orderId: {
            required: false,
            type: Number,
            default: 0,
        },
    },
    computed: {
        mainPage() {
            return this.$page.props.services.find(
                (service) => service.name == 'order',
            )
        },
    },

    methods: {
        isCurrentActive(page) {
            if (this.pages.length === 1) {
                return true
            }

            return (
                this.$route().current(page.matchRoute) ||
                (this.$route().current('orders.*') &&
                    this.orderOn === page.name)
            )
        },

        orderFilesLinksIsCurrentActive(page) {
            return (
                this.$route().current(page.matchRoute) ||
                (this.$route().current('orders.*') &&
                    this.orderOn === page.name)
            )
        },
    },
}
</script>
