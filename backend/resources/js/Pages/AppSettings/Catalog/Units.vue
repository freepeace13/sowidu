<script>
import CatalogItemUnitForm from './Components/CatalogItemUnitForm.vue'

export default {
    components: { CatalogItemUnitForm },

    props: {
        units: {
            required: true,
            type: Array,
        },
    },

    computed: {
        headers() {
            return [
                {
                    text: this.$t('app_settings.labels.form.unit-name'),
                    sortable: false,
                },
                {
                    text: this.$t('labels.actions'),
                    sortable: false,
                    align: 'center',
                },
            ]
        },
    },

    methods: {
        confirmDeleting(unit) {
            this.$confirm.ask({
                title: this.$t('labels.delete'),
                question: this.$t('app_settings.messages.confirm-delete-unit'),
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('app.settings.catalogs.units.destroy', {
                            unit,
                        }),
                        {
                            preserveState: true,
                            preserveScroll: true,
                        },
                    )
                },
            })
        },
    },
}
</script>
<template>
    <v-container
        fluid
        grid-list-md
    >
        <CatalogItemUnitForm ref="catalogItemUnitForm" />
        <v-layout
            row
            wrap
            align-center
            justify-space-between
        >
            <v-flex
                xs12
                class="headline shrink font-weight-bold"
            >
                <div class="tw-flex tw-items-center">
                    <v-btn
                        flat
                        icon
                        @click="$inertia.get($route('app.settings.index'))"
                    >
                        <v-icon>arrow_back</v-icon>
                    </v-btn>
                    <div>
                        {{ $t('app_settings.labels.catalog-unit-settings') }}
                    </div>
                </div>
                <v-divider />
            </v-flex>
            <v-flex xs6>
                <div class="tw-flex tw-justify-between">
                    <v-text-field
                        :label="$t('labels.search')"
                        prepend-icon="search"
                    />
                </div>
            </v-flex>
            <v-flex
                xs6
                class="tw-text-right"
            >
                <v-btn
                    color="primary"
                    @click="$refs.catalogItemUnitForm.show()"
                >
                    Add New
                </v-btn>
            </v-flex>

            <v-flex
                xs12
                mt-3
            >
                <v-data-table
                    :headers="headers"
                    :items="units"
                    class="elevation-5"
                    hide-actions
                    item-key="id"
                >
                    <template #items="{ item: unit }">
                        <tr>
                            <td>{{ unit.name }}</td>
                            <td class="tw-flex tw-justify-center">
                                <v-btn
                                    dark
                                    small
                                    color="info"
                                    @click="
                                        $refs.catalogItemUnitForm.show(unit)
                                    "
                                >
                                    <v-icon
                                        small
                                        dark
                                    >
                                        edit
                                    </v-icon>
                                    {{ $t('buttons.update') }}
                                </v-btn>
                                <v-btn
                                    dark
                                    small
                                    color="error"
                                    @click="confirmDeleting(unit)"
                                >
                                    <v-icon
                                        dark
                                        small
                                    >
                                        delete
                                    </v-icon>
                                    {{ $t('buttons.delete') }}
                                </v-btn>
                            </td>
                        </tr>
                    </template>
                </v-data-table>
            </v-flex>
        </v-layout>
    </v-container>
</template>
