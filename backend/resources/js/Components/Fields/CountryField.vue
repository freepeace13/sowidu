<template>
    <v-combobox
        v-model="selected"
        :items="items"
        :label="$t('labels.inputs.country')"
        :loading="isLoading"
        :search-input.sync="search"
        item-text="name"
        item-value="code"
        outline
        full-width
        v-bind="$attrs"
        @input="(input) => $emit('input', input)"
        @keyup.enter="isChanged"
        @blur="isChanged"
    >
        <template #item="{ item }">
            <v-list-tile-content>
                <v-list-tile-title>
                    {{ item?.flag }}
                    {{ item.name }}
                </v-list-tile-title>
            </v-list-tile-content>
        </template>
        <template #no-data>
            <v-list-tile @click="isChanged">
                <v-list-tile-content>
                    <v-list-tile-title class="tw-text-base">
                        No results matching "<strong>{{ search }}</strong
                        >".
                    </v-list-tile-title>
                    <v-list-tile-sub-title class="tw-text-sm">
                        Press <kbd>enter</kbd> to create a new one
                    </v-list-tile-sub-title>
                </v-list-tile-content>
            </v-list-tile>
        </template>
    </v-combobox>
</template>
<script>
import { useGetCountries } from '@/Composables/usePlaceService'
import { useDebounceFn } from '@vueuse/shared'

export default {
    props: {
        value: {
            type: [Object, String],
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
            if (!val || (!val?.code && !val?.name)) return
            this.selected = val

            this.items = [val]
        },

        async search(keyword) {
            if (this.isLoading) return

            await this.fetchResults(keyword)
        },
    },

    mounted() {
        this.fetchResults = useDebounceFn(async (keyword) => {
            try {
                this.isLoading = true
                const { data } = await useGetCountries(keyword)
                this.items = data
            } catch (error) {
                this.$root.$emit('flash.error', error)
            } finally {
                this.isLoading = false
            }
        }, 300)

        this.fetchResults()
    },
    methods: {
        isChanged() {
            if (this.selected?.name === this.search) return

            const newCountry = {
                name: this.search,
                code: this.search,
                isNew: true,
            }
            this.items.push(newCountry)
            this.$emit('input', newCountry)
        },
    },
}
</script>
