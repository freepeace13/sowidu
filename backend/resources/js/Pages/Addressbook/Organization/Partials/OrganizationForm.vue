<template>
    <v-dialog
        v-model="isShow"
        persistent
        max-width="600px"
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ title }}
                    {{ $tc('addressbook.labels.organization') }}
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
                        <v-flex
                            v-if="!isEditing"
                            xs12
                        >
                            <SearchInput
                                v-show="!organizationIsForeign"
                                v-model="organization"
                                :items="search.items"
                                :is-loading="search.isLoading"
                                :menu-props="{
                                    closeOnContentClick: true,
                                }"
                                @update:search-input="searchOrganization"
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
                                            {{ item.institution_type }}
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
                                        @mousedown.prevent="
                                            foreignOrganizationSelected
                                        "
                                        @click.stop.prevent="
                                            foreignOrganizationSelected
                                        "
                                    >
                                        <v-list-tile-avatar>
                                            <img
                                                :src="
                                                    defaultForeignCompanyAvatar
                                                "
                                            />
                                        </v-list-tile-avatar>
                                        <v-list-tile-content>
                                            <v-list-tile-title>
                                                {{
                                                    $t(
                                                        'addressbook.add-input-as-new-contact',
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
                            v-show="!isEditing && !organizationIsForeign"
                            class="my-2"
                        />
                        <v-expand-transition>
                            <div v-show="hasChosenOrganization">
                                <v-flex xs12>
                                    <v-text-field
                                        v-model="form.name"
                                        color="primary"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        :error-messages="form.errors.name"
                                        :label="$t('labels.inputs.name')"
                                        autofocus
                                        outline
                                        :hide-details="!form.errors.name"
                                        required
                                        class="required-input"
                                    />
                                </v-flex>
                                <v-flex xs12>
                                    <v-select
                                        v-model="form.legalform"
                                        :items="legalForms"
                                        :error-messages="form.errors.legalform"
                                        :hide-details="!form.errors.legalform"
                                        :label="$t('labels.inputs.legal-form')"
                                        item-text="name"
                                        item-value="name"
                                        outline
                                        required
                                        class="required-input"
                                    />
                                </v-flex>
                                <v-flex xs12>
                                    <v-select
                                        v-model="form.institution_type"
                                        :items="institutionTypes"
                                        :error-messages="
                                            form.errors.institution_type
                                        "
                                        :hide-details="
                                            !form.errors.institution_type
                                        "
                                        :label="
                                            $t('labels.inputs.institution-type')
                                        "
                                        item-text="name"
                                        item-value="name"
                                        outline
                                        required
                                        class="required-input"
                                    />
                                </v-flex>
                                <v-flex xs12>
                                    <v-text-field
                                        v-model="form.phone"
                                        color="primary"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        :error-messages="form.errors.phone"
                                        :label="$t('addressbook.phone')"
                                        autofocus
                                        outline
                                        :hide-details="!form.errors.phone"
                                    />
                                </v-flex>
                                <v-divider class="mt-2" />

                                <v-subheader class="subheading mt-2">
                                    {{ $t('addressbook.address') }}
                                </v-subheader>
                                <v-flex xs12>
                                    <AddressFields
                                        v-model="form.address"
                                        :is-loading="form.processing"
                                        :errors="form.errors"
                                    />
                                </v-flex>
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
                    {{ $t('buttons.cancel') }}
                </v-btn>
                <v-spacer />
                <v-btn
                    color="primary"
                    depressed
                    :disabled="form.processing || !hasChosenOrganization"
                    :loading="form.processing"
                    @click="save"
                >
                    {{ $t('buttons.save') }}
                    <template #loader>
                        <span>{{ $t('buttons.saving') }}</span>
                    </template>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import SearchInput from '../../../../Components/Fields/SearchInput.vue'
import { useDebounceFn } from '@vueuse/core'
import AddressFields from '@components/Fields/AddressFields.vue'
import {
    useNewOrganizationSearch,
    useShowOrganization,
} from '@/Apps/Addressbook/Composables/Organization'
import { useShowAddressbook } from '@/Composables/useAddressbook'

export default {
    components: { SearchInput, AddressFields },

    props: {
        legalForms: {
            type: Array,
            required: true,
        },
        institutionTypes: {
            required: true,
            type: Array,
        },
        requestProps: {
            type: Array,
            default: () => ['organizations', 'filters', 'errors'],
            required: false,
        },
    },
    data: (vm) => ({
        form: vm.$inertia.form({
            id: null,
            name: null,
            urn: null,
            legalform: null,
            institution_type: null,
            address: {
                house_number: null,
                street: null,
                zipcode: null,
                city: null,
                state: null,
                country: null,
            },
            phone: null,
            photo: null,
        }),
        organization: null,
        search: {
            isLoading: false,
            items: [],
            input: null,
        },
        organizationIsForeign: false,
        isShow: false,
        isEditing: false,
    }),

    computed: {
        hasChosenOrganization() {
            return this.organization || this.organizationIsForeign
        },

        defaultForeignCompanyAvatar() {
            return this.$page.props.defaults.avatars.foreign_organization
        },

        title() {
            return this.isEditing
                ? this.$t('buttons.update')
                : this.$t('labels.add') + this.$t('addressbook.contact')
        },
    },

    watch: {
        organization: {
            async handler(selected) {
                if (!selected || !selected?.id || this.isEditing) return

                const { data: organization } = await useShowOrganization(
                    selected.id,
                )
                this.setOrganization(organization)
            },
            deep: true,
        },
    },

    created() {
        this.searchOrganization = useDebounceFn(async (keyword) => {
            try {
                this.search.input = keyword
                if (!keyword) return

                this.search.isLoading = true
                this.search.items = []
                const { data } = await useNewOrganizationSearch(keyword, 5)
                this.search.items = data
            } catch (error) {
                this.$root.$emit('flash.error', error)
            } finally {
                this.search.isLoading = false
            }
        }, 500)
    },
    methods: {
        show(organization = null) {
            this.reset()

            if (!this.legalForms.length && !this.institutionTypes.length) {
                this.$inertia.reload({
                    only: ['legalForms', 'institutionTypes'],
                })
            }

            if (organization) {
                this.isEditing = true
                this.fetchAddressbook(organization)
            }

            this.isShow = true
        },

        async fetchAddressbook({ id }) {
            const { data } = await useShowAddressbook(id)
            this.organization = data
            this.setOrganization(data)
        },

        foreignOrganizationSelected() {
            this.organizationIsForeign = true
            this.form.name = this.search.input

            this.form.urn = null
            this.form.phone = null

            this.form.photo = this.defaultForeignCompanyAvatar
            this.form.institution_type = null
            this.form.legalform = null
            this.form.address = {
                house_number: null,
                street: null,
                zipcode: null,
                city: null,
                state: null,
                country: null,
            }
        },

        setOrganization(organization) {
            const {
                name,
                photo,
                address: { full, ...address },
                institution_type,
                legalform,
            } = organization

            this.form.urn = organization?.urn
            this.form.phone = organization?.phone
            console.log(organization)
            this.form.name = name
            this.form.photo = photo
            this.form.institution_type = institution_type
            this.form.legalform = legalform
            this.form.address = address
        },
        close() {
            this.isShow = false
            this.reset()
        },
        reset() {
            this.form.reset()
            this.form.clearErrors()

            this.organization = null
            this.isEditing = false
            this.organizationIsForeign = false
        },
        create() {
            const routeName = this.organizationIsForeign
                ? 'addressbooks.organization.foreign.store'
                : 'addressbooks.organizations.store'

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
                            this.$t('addressbook.messages.organization.added'),
                        )
                        this.close()
                    },
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                })
        },
        update() {
            this.form
                .transform((data) => ({
                    ...data,
                    address: {
                        ...data.address,
                        country: data.address.country?.code,
                    },
                }))
                .put(
                    this.$route('addressbooks.organizations.update', {
                        organization: this.organization,
                    }),
                    {
                        preserveState: true,
                        preserveScroll: true,
                        only: this.requestProps,
                        onSuccess: () => {
                            this.$root.$emit(
                                'flash.success',
                                this.$t(
                                    'addressbook.messages.organization.updated',
                                ),
                            )
                            this.close()
                        },
                        onError: (errors) =>
                            this.$root.$emit('flash.validation', errors),
                    },
                )
        },
        save() {
            this.$nextTick(() => {
                if (this.isEditing) return this.update()

                return this.create()
            })
        },
    },
}
</script>
