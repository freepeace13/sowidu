<template>
    <v-dialog
        v-model="isShow"
        persistent
        max-width="600px"
        lazy
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ isEditing ? 'Update' : 'Add' }} Contact Person
                </v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="close"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-divider />
            <v-card-text>
                <v-container
                    grid-list-lg
                    fluid
                    pa-2
                >
                    <v-layout column>
                        <v-flex v-if="!isEditing">
                            <SearchInput
                                v-show="!personIsForeign"
                                v-model="person"
                                :items="search.items"
                                :is-loading="search.isLoading"
                                :menu-props="{
                                    closeOnContentClick: false,
                                    closeOnClick: false,
                                }"
                                @update:search-input="searchPerson"
                            >
                                <template #item="item">
                                    <v-list-tile-avatar>
                                        <img :src="item.photo" />
                                    </v-list-tile-avatar>
                                    <v-list-tile-content>
                                        <v-list-tile-title>
                                            {{ item.name }}
                                        </v-list-tile-title>
                                        <v-list-tile-sub-title>
                                            {{ item.email }}
                                        </v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </template>
                                <template #no-data>
                                    <v-list-tile
                                        v-show="search.input"
                                        avatar
                                        ripple
                                        primary
                                        class="tw-cursor-pointer hover:tw-bg-grey-400"
                                        @mousedown.prevent="foreignerSelected"
                                        @click.stop.prevent="foreignerSelected"
                                    >
                                        <v-list-tile-avatar>
                                            <img
                                                :src="defaultForeignerAvatar"
                                            />
                                        </v-list-tile-avatar>
                                        <v-list-tile-content>
                                            <v-list-tile-title>
                                                {{
                                                    $t(
                                                        'add-input-as-new-contact',
                                                        { input: search.input },
                                                    )
                                                }}
                                            </v-list-tile-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                </template>
                            </SearchInput>
                        </v-flex>
                        <v-divider
                            v-if="!isEditing && !personIsForeign"
                            class="my-2"
                        />
                        <v-expand-transition>
                            <div v-show="hasChosenPerson">
                                <v-layout
                                    row
                                    wrap
                                    px-2
                                >
                                    <v-flex
                                        sm6
                                        xs12
                                    >
                                        <v-text-field
                                            v-model="form.first_name"
                                            color="primary"
                                            :loading="form.processing"
                                            :disabled="form.processing"
                                            :error-messages="
                                                form.errors.first_name
                                            "
                                            label="First Name"
                                            autofocus
                                            outline
                                            :hide-details="
                                                !form.errors.first_name
                                            "
                                            required
                                            class="required-input"
                                        />
                                    </v-flex>
                                    <v-flex
                                        sm6
                                        xs12
                                    >
                                        <v-text-field
                                            v-model="form.last_name"
                                            color="primary"
                                            :loading="form.processing"
                                            :disabled="form.processing"
                                            :error-messages="
                                                form.errors.last_name
                                            "
                                            label="Last Name"
                                            autofocus
                                            outline
                                            :hide-details="
                                                !form.errors.last_name
                                            "
                                            required
                                            class="required-input"
                                        />
                                    </v-flex>
                                </v-layout>
                                <v-flex>
                                    <v-text-field
                                        v-model="form.email"
                                        color="primary"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        :error-messages="form.errors.email"
                                        label="Email"
                                        autofocus
                                        outline
                                        :hide-details="!form.errors.email"
                                        required
                                        class="required-input"
                                    />
                                </v-flex>
                                <v-flex>
                                    <v-text-field
                                        v-model="form.phone"
                                        color="primary"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        :error-messages="form.errors.phone"
                                        label="Phone"
                                        autofocus
                                        outline
                                        :hide-details="!form.errors.phone"
                                        required
                                    />
                                </v-flex>
                                <v-flex
                                    v-show="isEditing"
                                    class="tw-flex tw-flex-col"
                                >
                                    <label class="body-2 mb-1">{{
                                        $t('addressbook.photo')
                                    }}</label>
                                    <AppAvatar
                                        :avatar="form.photo"
                                        :name="form.name"
                                    />
                                </v-flex>

                                <v-divider class="mt-2" />

                                <v-subheader class="subheading mt-2">
                                    {{ $t('addressbook-labels-address') }}
                                </v-subheader>

                                <v-layout column>
                                    <v-flex
                                        xs12
                                        px-3
                                    >
                                        <AddressFields
                                            v-model="form.address"
                                            :is-loading="form.processing"
                                            :errors="form.errors"
                                        />
                                    </v-flex>
                                </v-layout>
                            </div>
                        </v-expand-transition>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-divider />
            <v-card-actions class="px-4">
                <v-btn
                    :disabled="form.processing"
                    color="secondary"
                    depressed
                    @click="close"
                >
                    Cancel
                </v-btn>
                <v-spacer />
                <v-btn
                    color="primary"
                    depressed
                    :loading="form.processing"
                    :disabled="form.processing || !hasChosenPerson"
                    @click="submit"
                >
                    Save
                    <template #loader>
                        <span>Saving...</span>
                    </template>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import { useNewPersonSearch } from '@/Apps/Addressbook/Composables'
import { useShowProfile } from '@/Apps/Shared/Composables/useProfile'
import { useShowAddressbook } from '@/Composables/useAddressbook'
import FileInputMixin from '@/Mixins/FileInputMixin'
import AppAvatar from '@components/AppAvatar.vue'
import AddressFields from '@components/Fields/AddressFields.vue'
import { useDebounceFn } from '@vueuse/core'
import SearchInput from '../../../../Components/Fields/SearchInput.vue'
export default {
    components: { AddressFields, AppAvatar, SearchInput },

    mixins: [FileInputMixin],

    props: {
        requestProps: {
            type: Array,
            default: () => ['addressbooks', 'filters', 'errors'],
            required: false,
        },

        organization: {
            required: false,
            type: Object,
            default: () => ({}),
        },
    },

    data: (vm) => ({
        form: vm.$inertia.form({
            id: null,
            urn: null,
            first_name: null,
            last_name: null,
            email: null,
            photo: null,
            phone: null,
            address: {
                house_number: null,
                street: null,
                zipcode: null,
                city: null,
                state: null,
                country: null,
            },
        }),
        isShow: false,
        person: null,
        isEditing: false,
        personIsForeign: false,
        search: {
            isLoading: false,
            items: [],
            input: '',
        },
    }),

    computed: {
        hasChosenPerson() {
            return this.person || this.personIsForeign
        },

        defaultForeignerAvatar() {
            return this.$page.props.defaults.avatars.foreigner
        },
    },

    watch: {
        person: {
            async handler(selected) {
                if (!selected || !selected?.urn || this.isEditing) return

                const { data: person } = await useShowProfile(selected.urn)
                this.setPerson(person)
            },
            deep: true,
        },
    },

    created() {
        this.searchPerson = useDebounceFn(async (keyword) => {
            try {
                this.search.input = keyword
                if (!keyword) return

                this.search.isLoading = true
                this.search.items = []
                const { data } = await useNewPersonSearch(keyword, 5)
                this.search.items = data
            } catch (error) {
                this.$root.$emit('flash.error', error)
            } finally {
                this.search.isLoading = false
            }
        }, 500)
    },

    methods: {
        show(addressbook = null) {
            this.form.reset()
            this.isEditing = false

            if (addressbook) {
                this.fetchAddressbook(addressbook)
                this.isEditing = true
            }

            this.isShow = true
        },

        close() {
            this.isShow = false
            this.reset()
        },

        reset() {
            this.form.reset()
            this.form.clearErrors()

            this.person = null
            this.isEditing = false
            this.personIsForeign = false
        },

        async fetchAddressbook({ id }) {
            try {
                const { data } = await useShowAddressbook(id)
                this.setPerson(data)
                this.form.id = data.id
                this.person = data
            } catch (error) {
                this.$root.$emit('flash.error', error)
            }
        },

        foreignerSelected() {
            this.personIsForeign = true

            const [firstName, ...lastName] = this.search.input.split(' ')
            this.form.first_name = firstName
            this.form.last_name = lastName.join(' ')

            this.form.email = null
            this.form.photo = this.defaultForeignerAvatar
            this.form.phone = null
            this.form.address = {
                house_number: null,
                street: null,
                zipcode: null,
                city: null,
                state: null,
                country: null,
            }
        },

        setPerson(person) {
            const {
                email,
                first_name,
                last_name,
                photo,
                phone,
                address: { full, ...address },
            } = person

            this.form.urn = person?.urn

            this.form.first_name = first_name
            this.form.last_name = last_name
            this.form.email = email
            this.form.photo = photo
            this.form.phone = phone
            this.form.address = address
        },

        create() {
            const routeName = this.personIsForeign
                ? 'addressbooks.people.foreign.store'
                : 'addressbooks.people.store'
            this.form
                .transform((data) => ({
                    ...data,
                    address: {
                        ...data.address,
                        country: data.address.country?.code,
                    },
                }))
                .post(this.$route(routeName), {
                    preserveState: true,
                    preserveScroll: true,
                    only: this.requestProps,
                    onSuccess: () => {
                        this.$root.$emit(
                            'flash.success',
                            'Contact person has been created.',
                        )
                        this.close()
                    },
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                })
        },

        update() {
            const person = this.form.id

            this.form
                .transform((data) => ({
                    ...data,
                    address: {
                        ...data.address,
                        country: data.address.country?.code,
                    },
                }))
                .put(this.$route('addressbooks.people.update', { person }), {
                    preserveState: true,
                    preserveScroll: true,
                    only: this.requestProps,
                    onSuccess: () => {
                        this.$root.$emit(
                            'flash.success',
                            'Contact person has been updated.',
                        )
                        this.close()
                    },
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                })
        },

        async submit() {
            try {
                const isEditing = this.isEditing

                if (this.personIsForeign) return this.create()

                if (!this.person.urn && !isEditing) return

                if (isEditing) return this.update()

                return this.create()
            } catch (error) {
                this.$root.$emit('flash.error', error)
            } finally {
                // this.form.processing = false
            }
        },
    },
}
</script>
