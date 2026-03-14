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
                    {{ $t('order.labels.update-client') }}
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
                        <SearchInput
                            v-show="!clientIsForeign"
                            ref="searchInputRef"
                            v-model="client"
                            :items="search.items"
                            :is-loading="search.isLoading"
                            :menu-props="{
                                closeOnContentClick: true,
                            }"
                            placeholder="Search client name or email..."
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
                                <v-list-tile-action>
                                    <v-icon
                                        v-if="!item?.legalform"
                                        small
                                        color="primary"
                                    >
                                        account_circle
                                    </v-icon>
                                    <v-icon
                                        v-if="item?.legalform"
                                        small
                                        color="secondary"
                                    >
                                        business
                                    </v-icon>
                                </v-list-tile-action>
                            </template>
                            <template #no-data>
                                <v-list-tile
                                    v-show="search.input"
                                    avatar
                                    ripple
                                    primary
                                    class="tw-cursor-pointer hover:tw-bg-grey-400"
                                >
                                    <v-list-tile-avatar>
                                        <img :src="defaultClientAvatar" />
                                    </v-list-tile-avatar>
                                    <v-list-tile-content
                                        @click="foreignerSelected"
                                    >
                                        <v-list-tile-title>
                                            Add "{{ search.input }}" as new
                                            Contact
                                        </v-list-tile-title>
                                    </v-list-tile-content>
                                </v-list-tile>
                            </template>
                        </SearchInput>
                        <v-divider
                            v-if="!clientIsForeign"
                            class="my-2"
                        />
                        <v-expand-transition>
                            <!-- Client Details -->
                            <div v-show="hasChosenClient">
                                <ForeignClientForm
                                    v-if="clientIsForeign"
                                    v-model="form"
                                />
                                <ClientDetails
                                    v-if="!clientIsForeign"
                                    v-model="form"
                                />
                            </div>
                        </v-expand-transition>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-divider />
            <v-card-actions class="px-4">
                <v-btn
                    :disabled="form.processing"
                    outline
                    depressed
                    @click="close"
                >
                    Cancel
                </v-btn>
                <v-spacer />
                <SubmitButton
                    :loading="form.processing"
                    :disabled="form.processing || !hasChosenClient"
                    @click="submit"
                >
                    {{ $t('buttons.update') }}
                    <template #loader>
                        <span>{{ $t('buttons.updating') }}...</span>
                    </template>
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import {
    useSearchAddressbook,
    useShowAddressbook,
} from '@/Composables/useAddressbook'
import { useDateNow } from '@/Composables/useDayJs'
import { useDefaultAddress } from '@/Composables/useDefaults'
import SubmitButton from '@components/Forms/SubmitButton.vue'
import { useDebounceFn } from '@vueuse/core'
import ClientDetails from '../Incoming/Components/ClientDetails.vue'
import ForeignClientForm from '../Incoming/Components/ForeignClientForm.vue'
import SearchInput from '@/Components/Fields/SearchInput.vue'

export default {
    components: {
        SearchInput,
        SubmitButton,
        ForeignClientForm,
        ClientDetails,
    },

    data: (vm) => ({
        form: vm.$inertia.form({
            client: {
                address: vm.getDefaultAddress(),
            },
            client_id: null,
        }),
        isShow: false,
        client: null,
        order: null,
        clientIsForeign: false,
        search: {
            isLoading: false,
            items: [],
            input: '',
        },
    }),

    computed: {
        hasChosenClient() {
            return this.client || this.clientIsForeign
        },

        defaultClientAvatar() {
            return this.$page.props.defaults.avatars.foreign_client
        },
    },

    watch: {
        client: {
            async handler(selected) {
                if (!selected || !selected?.id) return

                const { data: client } = await useShowAddressbook(selected.id)
                this.setClient(client)
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
                const { data } = await useSearchAddressbook(keyword, 5)
                this.search.items = data
            } catch (error) {
                this.$root.$emit('flash.error', error)
            } finally {
                this.search.isLoading = false
            }
        }, 500)
    },

    methods: {
        async show(order) {
            this.reset()

            this.order = order

            this.isShow = true

            await this.fetchClientAddressbook(order.client_addressbook_id)
        },

        async fetchClientAddressbook(clientAddressbook) {
            const { data } = await useShowAddressbook(clientAddressbook)

            this.client = data
            this.search.items = [data]
            this.$refs.searchInputRef.assignSelected(data)
        },

        close() {
            this.isShow = false
            this.reset()
        },

        getDefaultAddress() {
            return useDefaultAddress
        },

        defaultDate() {
            return useDateNow()
        },

        reset() {
            this.form.reset()
            this.form.clearErrors()

            this.client = null
            this.order = null

            this.clientIsForeign = false
        },

        foreignerSelected() {
            this.clientIsForeign = true
            const [first_name, ...lastName] = this.search.input.split(' ')

            this.setClient({
                first_name,
                last_name: lastName.length ? lastName.join(' ') : '',
                email: null,
                phone: null,
                address: this.getDefaultAddress(),
            })
        },

        setClient(client) {
            this.form.client = client
        },

        submit() {
            if (!this.clientIsForeign) {
                this.form.client_id = this.client.id
            }

            if (this.clientIsForeign) {
                // Add delivery-address
                // let delivery_address = this.form.delivery_address
                // if (this.deliverySameToCurrentAddress) {
                //     delivery_address = this.form.client.address
                // }
                // this.form.transform((data) => ({
                //     ...data,
                //     delivery_address,
                //     client: {
                //         ...data.client,
                //         address: {
                //             ...data.client.address,
                //             country: data.client.address.country?.code,
                //         },
                //     },
                // }))
            }

            const order = this.order

            this.form
                .transform((data) => ({
                    ...data,
                    description: order.description,
                    order_date: order.order_date,
                    planned_start_date: order.planned_start_date,
                    planned_finish_date: order.planned_finish_date,
                }))
                .patch(this.$route('orders.incoming.update', { order }), {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['errors', 'order', 'flash'],
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                    onSuccess: () => this.close(),
                })
        },
    },
}
</script>
<style scoped>
.v-expansion-panel__header {
    padding-left: 0px;
    padding-right: 0px;
}
</style>
