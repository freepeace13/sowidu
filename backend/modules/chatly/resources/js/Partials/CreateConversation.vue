<template>
    <v-dialog
        v-model="isShow"
        max-width="500"
        :persistent="true"
    >
        <v-card>
            <v-card-title
                class="headline grey lighten-2"
                primary-title
            >
                {{ $t('chat.create-new-conversation') }}
            </v-card-title>
            <v-card-text>
                <v-container grid-list-md>
                    <v-layout wrap>
                        <v-flex xs12>
                            <v-autocomplete
                                ref="selectParticipant"
                                v-model="participants"
                                :items="items"
                                :search-input.sync="keyword"
                                :loading="isLoading"
                                :placeholder="
                                    $t('chat.search-people-or-organizations')
                                "
                                :no-data-text="$t('chat.nothing-found')"
                                background-color="grey lighten-4"
                                multiple
                                deletable-chips
                                chips
                                :no-filter="true"
                                @input="participantSelected"
                            >
                                <template #prepend-item>
                                    <v-list-tile>
                                        <v-list-tile-avatar>
                                            <v-icon>search</v-icon>
                                        </v-list-tile-avatar>
                                        <v-list-tile-content>
                                            <v-list-tile-title>
                                                {{
                                                    $t('search-for-keyword', {
                                                        keyword,
                                                    })
                                                }}
                                            </v-list-tile-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                </template>

                                <template #selection="data">
                                    <v-chip
                                        :selected="data.selected"
                                        close
                                        class="chip--select-multi"
                                        @input="remove(data.item)"
                                    >
                                        <v-avatar>
                                            <img :src="data.item.photo" />
                                        </v-avatar>
                                        {{ data.item.name }}
                                    </v-chip>
                                </template>
                                <template #item="data">
                                    <template
                                        v-if="typeof data.item === 'object'"
                                    >
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
                                        <v-list-tile-content>{{
                                            data.item
                                        }}</v-list-tile-content>
                                    </template>
                                </template>
                            </v-autocomplete>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-divider />
            <v-card-actions class="text-lg-right grey lighten-4">
                <v-spacer />
                <v-btn
                    flat
                    @click="isShow = false"
                >
                    {{ $t('buttons.cancel') }}
                </v-btn>
                <v-btn
                    color="primary"
                    depressed
                    :disabled="!participants.length"
                    @click="save"
                >
                    {{ $t('buttons.create') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import Http from '@/Modules/Http'
import { debounce } from 'lodash'

export default {
    data: () => ({
        items: [],
        participants: [],
        keyword: '',
        isLoading: false,
        isShow: false,
    }),

    watch: {
        keyword(value) {
            if (!value) return
            this.searchUsers({ keyword: value })
        },
    },

    mounted() {
        this.searchUsers = debounce((params) => {
            this.isLoading = true
            this.items = []
            Http.get(this.$route('chatly.search', params))
                .then(({ data }) => {
                    this.isLoading = false
                    if (!data.groups.length && !data.people.length) return
                    const rawItems = [
                        { header: 'People' },
                        ...data.people,
                        ...Object.keys(data.groups).map((group) => [
                            { header: group },
                            ...data.groups[group],
                        ]),
                    ]

                    this.items = rawItems.flat()

                    this.seedSelectedParticipants()
                })
                .catch(console.error)
                .finally(() => {
                    this.isLoading = false
                })
        }, 500)

        this.$once(
            'hook:destroyed',
            this.$inertia.on('finish', () => {
                this.$root.$emit('chats-list-refresh')
                this.$root.$emit('chat-messages-reload')
            }),
        )
    },

    methods: {
        show() {
            this.isShow = true
            this.items = []
            this.participants = []
            this.keyword = ''
        },

        remove(item) {
            const index = this.participants.indexOf(item)
            if (index >= 0) this.participants.splice(index, 1)
            this.seedSelectedParticipants()
        },

        save() {
            const participants = this.participants
            this.$inertia
                .form({ participants })
                .post(this.$route('chatly.store'))
            this.isShow = false

            this.items = []
            this.participants = []
            this.keyword = ''
        },

        participantSelected(item) {
            this.participants = item
            this.seedSelectedParticipants()
        },

        seedSelectedParticipants() {
            if (this.participants.length) {
                this.$nextTick(() => {
                    this.$refs.selectParticipant.selectedItems =
                        this.participants
                })
            }
        },
    },
}
</script>
