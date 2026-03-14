<template>
    <v-dialog
        v-model="show"
        max-width="650"
    >
        <v-card>
            <v-card-title class="title">
                <v-layout align-center>
                    <div class="tw-flex tw-flex-wrap tw-items-center">
                        {{ $t('headings.invite-member') }}
                    </div>

                    <v-btn
                        class="ml-auto"
                        icon
                        @click="close"
                    >
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-layout>
            </v-card-title>

            <v-card-text>
                <v-container
                    class="pa-0"
                    fluid
                    grid-list-lg
                    fill-height
                >
                    <v-layout column>
                        <v-flex>
                            <v-autocomplete
                                ref="input"
                                v-model="form.email"
                                :items="items"
                                :search-input.sync="search"
                                :loading="searching"
                                :error="!!form.errors.email"
                                item-text="email"
                                item-value="email"
                                :placeholder="`${$t(
                                    'hints.search-username-fullname-or-email',
                                )} *`"
                                :label="$t('labels.inputs.members')"
                                outline
                                no-filter
                                hide-details
                                hide-no-data
                                single-line
                                required
                            >
                                <template #selection="{ item }">
                                    <v-list-tile-avatar>
                                        <img
                                            :src="item.photo"
                                            class="w-border"
                                        />
                                    </v-list-tile-avatar>
                                    <v-list-tile-content>
                                        {{ item.email }}
                                    </v-list-tile-content>
                                </template>

                                <template #item="{ item }">
                                    <v-list-tile-avatar>
                                        <img
                                            :src="item.photo"
                                            class="w-border"
                                        />
                                    </v-list-tile-avatar>

                                    <v-list-tile-content>
                                        <v-list-tile-title>
                                            {{
                                                item.anonymous
                                                    ? item.email
                                                    : item.name
                                            }}
                                        </v-list-tile-title>

                                        <v-list-tile-sub-title
                                            v-if="!item.anonymous"
                                        >
                                            {{ item.email }}
                                        </v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </template>
                            </v-autocomplete>
                        </v-flex>

                        <v-flex>
                            <v-combobox
                                v-model="form.role"
                                :items="roles"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.role"
                                :hide-details="!form.errors.role"
                                :label="$t('labels.inputs.role')"
                                outline
                                required
                                :search-input.sync="newRole"
                            >
                                <template #no-data>
                                    <v-list-tile>
                                        <v-list-tile-content>
                                            <v-list-tile-title>
                                                {{
                                                    $t(
                                                        'hints.no-results-matching',
                                                        {
                                                            keyword: newRole,
                                                        },
                                                    )
                                                }}
                                                Press <kbd>enter</kbd> to create
                                                a new one.
                                            </v-list-tile-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                </template>
                            </v-combobox>
                        </v-flex>

                        <v-flex>
                            <v-textarea
                                v-model="form.note"
                                color="primary"
                                hide-details
                                :label="$t('hints.invite-message-to-user')"
                                outline
                            />
                        </v-flex>

                        <v-btn
                            :loading="form.processing"
                            :disabled="form.processing"
                            color="primary"
                            large
                            @click="sendInvitation"
                        >
                            {{ $t('buttons.invite') }}
                            <template #loader>
                                <span>{{
                                    $t('account.buttons.inviting')
                                }}</span>
                            </template>
                        </v-btn>
                    </v-layout>
                </v-container>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
import { useDebounceFn } from '@vueuse/core'
import axios from 'axios'

export default {
    name: 'AddMemberForm',

    props: {
        roles: {
            type: Array,
            required: true,
        },
    },

    data: (vm) => ({
        show: false,

        search: null,
        searching: false,
        items: [],
        newRole: null,

        form: vm.$inertia.form({
            email: null,
            note: null,
            role: null,
        }),
    }),

    watch: {
        search(value) {
            if (typeof value == 'string') {
                this.fetchItems({ _query: { search: value } })
            }
        },
    },

    mounted() {
        this.fetchItems = useDebounceFn(async (params) => {
            this.items = []
            this.searching = true

            const { data } = await axios.get(
                this.$route('apps.employees.users', params),
            )

            const users = Object.values(data.users)
            this.items = users

            if (!users.length) {
                this.items = [{ anonymous: true, email: this.search }]
            }

            this.searching = false
        }, 500)
    },

    methods: {
        start() {
            this.form.reset()
            this.form.clearErrors()

            this.search = null
            this.searching = false
            this.items = []

            this.show = true
        },

        close() {
            this.form.reset()

            this.search = null
            this.searching = false
            this.items = []

            this.show = false
        },

        sendInvitation() {
            // this.form.clearErrors()

            this.form.post(this.$route('account.employees.invitations.store'), {
                onSuccess: () => this.close(),
                onError: (errors) =>
                    this.$root.$emit('flash.validation', errors),
            })
        },
    },
}
</script>
