<template>
    <v-dialog
        v-model="isShow"
        max-width="650"
        lazy
    >
        <v-card>
            <v-card-title>
                <span class="title">Tag to Address</span>
                <v-spacer />
                <v-btn
                    icon
                    @click="close"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-container
                    fluid
                    py-0
                    px-2
                    grid-list-lg
                >
                    <v-layout
                        v-show="!isShowAddressForm"
                        wrap
                    >
                        <v-flex>
                            <AddressAutocomplete
                                v-model="form.address"
                                outline
                                color="primary"
                                :placeholder="
                                    $t('labels.inputs.search-address')
                                "
                                single-line
                                :disabled="form.processing"
                                :loading="form.processing"
                                :menu-props="{
                                    closeOnContentClick: true,
                                }"
                                @click:create-new="showAddressForm"
                            />
                        </v-flex>
                    </v-layout>
                    <v-layout
                        v-if="isShowAddressForm"
                        column
                    >
                        <v-flex>
                            <v-text-field
                                :loading="form.processing"
                                readonly
                                :label="
                                    $t('labels.inputs.complete-address-preview')
                                "
                                outline
                                :hide-details="true"
                                :value="completeAddress"
                            />
                        </v-flex>

                        <v-flex xs12>
                            <AddressFields
                                v-model="form.address"
                                :is-loading="form.processing"
                                :errors="form.errors"
                            />
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>

            <v-card-actions class="px-3">
                <v-spacer />
                <v-btn
                    color="primary"
                    :disabled="form.processing || !completeAddress"
                    :loading="form.processing"
                    @click="submit"
                >
                    {{ $t('buttons.tag') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import AddressAutocomplete from '@/Apps/Shared/Components/AutoComplete/AddressAutocomplete.vue'
import AddressFields from '@components/Fields/AddressFields.vue'
import axios from 'axios'

export default {
    components: { AddressAutocomplete, AddressFields },

    data: (vm) => ({
        isShow: false,
        media: null,
        form: vm.$inertia.form({
            address: {
                house_number: null,
                street: null,
                city: null,
                state: null,
                country: null,
                zipcode: null,
            },
        }),
        isShowAddressForm: false,
    }),

    computed: {
        completeAddress() {
            if (!this.form.address) return

            const { country, zipcode, house_number, street, city, state } =
                this.form.address
            let countryName = country?.name

            return Object.values({
                street,
                house_number,
                zipcode,
                city,
                countryName,
                state,
            })
                .filter(Boolean)
                .join(', ')
        },
    },

    methods: {
        show(media) {
            if (!media) return

            this.fetchAddressTags(media)

            this.isShow = true
            this.media = media
        },
        async fetchAddressTags({ uuid: media }) {
            const { data } = await axios.get(
                this.$route('json.media.address_tags.show', {
                    media,
                }),
            )
            this.form.address = data?.full
        },

        close() {
            this.isShow = false
            this.isShowAddressForm = false
            this.media = null
            this.form.reset()
        },
        submit() {
            this.form
                .transform((data) => ({
                    ...data,
                    address: {
                        ...data.address,
                        country: data.address.country?.code,
                    },
                }))
                .post(
                    this.$route('media.tag_address.store', {
                        media: this.media.uuid,
                    }),
                    {
                        preserveState: true,
                        preserveScroll: true,
                        only: ['errors'],
                        onSuccess: () => {
                            this.$root.$emit(
                                'flash.success',
                                this.$t(
                                    'media.message.address-has-been-tagged-to-media',
                                ),
                            )
                            this.$emit('refresh-media', this.media.uuid)
                            this.close()
                        },
                        onError: (errors) =>
                            this.$root.$emit('flash.validation', errors),
                    },
                )
        },
        showAddressForm() {
            this.form.reset()
            this.isShowAddressForm = true
        },
    },
}
</script>
