<script>
import { isNull } from '@/Composables/useUtils'
import { useDebounceFn } from '@vueuse/core'
import axios from 'axios'

export default {
    props: {
        value: {
            type: [Object, Array],
            required: false,
            default: null,
        },
    },

    data: (vm) => ({
        selected: vm.value,
        search: null,
        items: [],
        isLoading: false,
    }),

    watch: {
        value(val) {
            if (!val) {
                this.selected = null
            }

            if (val) {
                this.setSelectedValue(val)
            }
        },
    },

    mounted() {
        if (!isNull(this.value)) {
            this.setSelectedValue(this.value)
        }

        this.fetch = useDebounceFn(async (params) => {
            if (!params?.q) return

            try {
                this.items = []
                this.isLoading = true

                const { data } = await axios.get(
                    this.$route('json.autocomplete.addressbook', params),
                )
                this.items = data
            } catch (error) {
                console.error(error)
            } finally {
                this.isLoading = false
            }
        }, 500)
    },

    methods: {
        setSelectedValue(value) {
            this.items.push(value)
            this.selected = value
        },
    },
}
</script>
<template>
    <v-autocomplete
        v-model="selected"
        v-bind="$attrs"
        :search-input.sync="search"
        :loading="isLoading"
        :items="items"
        :no-filter="true"
        prepend-inner-icon="perm_contact_calendar"
        outline
        single-line
        allow-overflow
        hide-selected
        return-object
        color="primary"
        item-text="name"
        @update:searchInput="(q) => fetch({ q })"
        @input="(val) => $emit('input', val)"
    >
        <template #selection="data">
            <slot
                name="selection"
                v-bind="data"
            >
                <v-chip
                    label
                    disabled
                    selected
                >
                    <v-avatar>
                        <img :src="data.item.column_values?.photo" />
                    </v-avatar>
                    {{ data.item.column_values?.name }}
                </v-chip>
            </slot>
        </template>
        <template #item="{ item, item: { column_values } }">
            <slot
                name="item"
                v-bind="item"
            >
                <v-list-tile-avatar>
                    <img :src="column_values?.photo" />
                </v-list-tile-avatar>
                <v-list-tile-content>
                    <v-list-tile-title>
                        {{ column_values.name }}
                    </v-list-tile-title>
                    <v-list-tile-sub-title>
                        {{ column_values?.email }}
                    </v-list-tile-sub-title>
                </v-list-tile-content>
            </slot>
        </template>
        <template #no-data>
            <slot name="no-data">
                <v-list-tile
                    avatar
                    ripple
                >
                    <v-list-tile-avatar />
                    <v-list-tile-content>
                        <v-list-tile-title>
                            {{ $t('hints.nothing-found') }}
                        </v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </slot>
        </template>
    </v-autocomplete>
</template>
