<template>
    <v-combobox
        v-model="selected"
        outline
        no-filter
        full-width
        hide-no-data
        append-icon="none"
        :items="items"
        :loading="isLoading"
        :search-input.sync="search"
        v-bind="$attrs"
        @input="(input) => $emit('input', input)"
    />
</template>

<script>
import { useDebounceFn } from '@vueuse/shared'
import { useAutocompleteHouseNumber } from '@/Composables/useAutocomplete'

export default {
    props: {
        value: {
            type: String,
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
            this.selected = val

            if (!val) return

            this.items = [val]
        },

        async search(keyword) {
            this.$emit('input', keyword)
            if (this.isLoading) return

            await this.fetchResults(keyword)
        },
    },

    mounted() {
        this.fetchResults = useDebounceFn(async (keyword) => {
            try {
                if (!keyword) return
                this.isLoading = true

                const { data } = await useAutocompleteHouseNumber(keyword, 5)
                this.items = data
            } catch (error) {
                this.$root.$emit('flash.error', error)
            } finally {
                this.isLoading = false
            }
        }, 300)
    },
}
</script>
