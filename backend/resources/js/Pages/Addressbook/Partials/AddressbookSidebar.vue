<template>
    <v-navigation-drawer
        permanent
        fixed
        app
        stateless
        :mini-variant="$vuetify.breakpoint.smAndDown"
        navbar-top-margin
    >
        <v-toolbar
            flat
            class="transparent"
        >
            <v-toolbar-title class="title tw-flex tw-items-center">
                {{ $t('labels.addressbook') }}
                <v-icon
                    v-if="$vuetify.breakpoint.xs"
                    :color="mainPage?.color"
                    x-large
                >
                    {{ mainPage?.icon }}
                </v-icon>
                {{ $vuetify.breakpoint.xs ? '' : $t(mainPage?.label) }}
            </v-toolbar-title>
        </v-toolbar>
        <v-list class="pa-0">
            <v-divider />

            <template v-for="page in pages">
                <SidebarItem
                    :key="page.title"
                    :label="page.title"
                    :icon="page.icon"
                    :is-active="
                        $page.url === $route(page.route, null, false) ||
                        $route().current(
                            `addressbooks.${page.title.toLowerCase()}.*`,
                        ) ||
                        $route().current(page.route)
                    "
                    :url="$route(page.route)"
                />
                <v-divider :key="`link-divider-${page.title}`" />
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
    },

    computed: {
        mainPage() {
            const services = this.$page.props?.services

            if (services) return

            return services?.find((service) => service.name == 'addressbook')
        },
    },
}
</script>
