<script>
import { isNull } from '@/Composables/useUtils'
import { useDebounceFn } from '@vueuse/core'
import axios from 'axios'

export default {
    props: {
        value: {
            type: Object,
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

        search(q, oldVal) {
            if (isNull(oldVal) && !isNull(q) && !isNull(this.value)) {
                return
            }

            if (!isNull(oldVal) && isNull(q)) {
                return
            }

            this.fetch({ q })
        },
    },

    mounted() {
        if (!isNull(this.value)) {
            this.setSelectedValue(this.value)
        }

        this.fetch = useDebounceFn(async (params) => {
            const q = params?.q

            if (isNull(q)) return

            try {
                this.items = []
                this.isLoading = true

                const { data } = await axios.get(
                    this.$route('json.autocomplete.address', {
                        text: q,
                        size: 10,
                    }),
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
            this.selected = value
            this.items.push(value)
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
        prepend-inner-icon="home"
        outline
        single-line
        allow-overflow
        hide-selected
        return-object
        color="primary"
        item-text="full"
        @input="(val) => $emit('input', val)"
    >
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
