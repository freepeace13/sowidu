<template>
    <v-breadcrumbs :items="items" large class="pa-0">
        <template #divider>
            <v-icon size="18" color="grey darken-1">navigate_next</v-icon>
        </template>

        <template v-slot:item="{ item }">
            <v-menu v-if="!$page.props.folder.exists || item.uuid == $page.props.folder.uuid" close-on-content-click min-width="260" offset-y>
                <template v-slot:activator="{ on }">
                    <v-btn flat class="text-capitalize title font-weight-regular mx-0" v-on="on">
                        {{ label }}
                        <v-icon right color="grey darken-1">
                            arrow_drop_down
                        </v-icon>
                    </v-btn>
                </template>

                <slot name="menu" v-bind="{ item }" />
            </v-menu>

            <v-btn v-else flat class="text-capitalize title font-weight-regular mx-0" @click="$inertia.visit(item.route)">
                {{ label }}
            </v-btn>
        </template>
    </v-breadcrumbs>
</template>

<script>
export default {
    computed: {
        items() {
            const folders = this.$page.props.tree

            return [
                { name: this.$t('headings.my-drive'), key: null, route: this.$route('media.drive.index') },
                ...folders.map((f) => ({
                    name: f.name,
                    uuid: f.uuid,
                    route: this.$route('media.drive.folders.show', {
                        folder: f.uuid,
                    }),
                })),
            ]
        },

        label() {
            return this.$page.props.type
        }
    },
}
</script>
