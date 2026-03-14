<template>
    <v-autocomplete
        ref="input"
        flat
        solo
        hide-details
        :items="items"
        :search-input.sync="keyword"
        :menu-props="{ maxWidth: 310, contentClass: 'elevation-1' }"
        append-icon=""
        no-data-text=""
        no-filter
        :hide-no-data="!keyword"
        :prepend-inner-icon="icon"
        background-color="grey lighten-4"
        :placeholder="$t('chat.search-people-or-organizations')"
        @click:prepend-inner="onReset()"
        @focus="switchIcon"
        @blur="switchIcon"
        @input="onSelect"
    >
        <template #prepend-item>
            <v-list-tile>
                <v-list-tile-avatar>
                    <v-icon>search</v-icon>
                </v-list-tile-avatar>
                <v-list-tile-content>
                    <v-list-tile-title>
                        {{ $t('chat.search-for-keyword', { keyword }) }}
                    </v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>
        </template>

        <template #item="data">
            <template v-if="typeof data.item === 'object'">
                <v-list-tile-avatar>
                    <v-img :src="data.item.photo" />
                </v-list-tile-avatar>
                <v-list-tile-content>
                    <v-list-tile-title>
                        {{ data.item.name }}
                        <span
                            v-if="!data.item.is_user"
                            class="font-weight-bold grey--text text-capitalize"
                        >
                            - {{ data.item.role }}
                        </span>
                    </v-list-tile-title>
                </v-list-tile-content>
            </template>
            <template v-else>
                <v-list-tile-content>{{ data.item }}</v-list-tile-content>
            </template>
        </template>
    </v-autocomplete>
</template>

<script>
import axios from 'axios'
import { debounce } from 'lodash'

export default {
    data: () => ({
        items: [],
        keyword: null,
        icon: 'search',
    }),

    watch: {
        keyword(value) {
            if (!value) return

            this.fetchItems({ keyword: value })
        },
    },

    mounted() {
        this.fetchItems = debounce(async (params) => {
            this.items = []

            const {
                data: { groups, people },
            } = await axios.get(this.$route('chatly.search', params))

            const rawItems = [
                { header: 'People' },
                ...people,
                ...Object.keys(groups).map((group) => [
                    { header: group },
                    ...groups[group],
                ]),
            ]

            this.items = rawItems.flat()
        }, 500)
    },

    methods: {
        onSelect(value) {
            if (value === undefined) return

            const participants = [{ id: value.id, type: value.type }]

            this.$inertia
                .form({ participants })
                .post(this.$route('chatly.store'))

            this.onReset()
        },

        onReset() {
            this.items = []
            this.icon = 'search'
            this.keyword = null

            this.$refs.input.setValue(undefined)
            this.$refs.input.blur()
        },

        switchIcon() {
            this.$nextTick(() => {
                if (this.$refs.input.isFocused) {
                    this.icon = 'arrow_back'
                } else {
                    this.icon = 'search'
                }
            })
        },
    },
}
</script>
