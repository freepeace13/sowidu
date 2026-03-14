<template>
    <!-- v-bind="$attrs" -->
    <v-autocomplete
        v-model="selected"
        :loading="isLoading"
        :items="items"
        :no-filter="true"
        :placeholder="placeholderString"
        prepend-inner-icon="search"
        outline
        single-line
        hide-details
        allow-overflow
        hide-selected
        return-object
        color="primary"
        item-text="name"
        item-value="urn"
        @update:searchInput="(keyword) => $emit('update:search-input', keyword)"
        @input="save"
        @focus="$emit('focus', $event)"
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
                        <img :src="data.item.photo" />
                    </v-avatar>
                    {{ data.item.name }}
                </v-chip>
            </slot>
        </template>
        <template #item="{ item }">
            <slot
                name="item"
                v-bind="item"
            >
                <v-list-tile-avatar>
                    <img :src="item.photo" />
                </v-list-tile-avatar>
                <v-list-tile-content>
                    <v-list-tile-title>{{ item.name }}</v-list-tile-title>
                    <v-list-tile-sub-title>
                        {{ item.email }}
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
                        <v-list-tile-title>Nothing found.</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </slot>
        </template>
    </v-autocomplete>
</template>
<script>
export default {
    props: {
        value: {
            type: Object,
            required: false,
            default: null,
        },
        items: {
            type: Array,
            required: true,
        },
        isLoading: {
            type: Boolean,
            default: false,
            required: false,
        },
        placeholder: {
            required: false,
            type: String,
            default: null,
        },
        useAddressbook: {
            required: false,
            type: Boolean,
            default: false,
        },
    },

    data: () => ({
        selected: null,
    }),

    computed: {
        placeholderString() {
            return this.placeholder ?? this.$t('hints.quick-search')
        },
    },

    watch: {
        value(val) {
            if (!val) {
                this.selected = null
            }
        },

        selected(selectedValue) {
            this.$emit('input', selectedValue)
        },
    },

    methods: {
        async save() {
            if (!this.selected?.urn) {
                this.$emit('input', null)
                return
            }

            this.$emit('input', this.selected)
        },

        assignSelected(selected) {
            this.selected = selected
        },
    },
}
</script>
