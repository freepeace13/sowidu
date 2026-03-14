<template>
    <v-dialog
        v-model="isShow"
        max-width="650"
        lazy
        persistent
    >
        <v-card>
            <v-card-title>
                <span class="title">
                    {{ $t('order.labels.upload-order-media') }}
                </span>
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
                            <v-label>
                                {{ $t('order.labels.tag-address') }}
                            </v-label>
                            <AddressAutocomplete
                                ref="addressAutoComplete"
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
                                :disabled="form.processing"
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
                        <v-flex
                            justify-end
                            class="tw-flex"
                        >
                            <v-btn
                                color="primary"
                                @click="saveAndSelectAddress"
                            >
                                {{ $t('buttons.done') }}
                            </v-btn>
                        </v-flex>
                    </v-layout>
                    <v-layout column>
                        <v-flex>
                            <v-label>
                                {{ $t('order.labels.tag-category') }}
                            </v-label>
                            <v-select
                                v-model="form.category"
                                :items="categories"
                                outline
                                solo
                                clearable
                                :disabled="form.processing"
                                :loading="form.processing"
                                :hide-details="!form.errors.category"
                                :error-messages="form.errors.category"
                                @input="submit"
                            />
                        </v-flex>
                        <v-flex>
                            <v-label>
                                {{ $t('order.labels.add-files') }}
                            </v-label>
                        </v-flex>
                    </v-layout>
                    <v-container
                        grid-list-sm
                        fluid
                        pt-0
                        px-0
                        mt-4
                    >
                        <v-layout
                            row
                            wrap
                        >
                            <v-flex
                                v-for="(file, fileIndex) in files"
                                :key="fileIndex"
                                xs4
                                d-flex
                            >
                                <v-card
                                    flat
                                    tile
                                    class="d-flex"
                                    hover
                                >
                                    <v-img
                                        :src="file.url"
                                        aspect-ratio="1"
                                        class="grey lighten-2"
                                    />
                                </v-card>
                            </v-flex>
                            <v-flex
                                xs4
                                d-flex
                                class="tw-items-center"
                            >
                                <v-card
                                    tile
                                    flat
                                    class="tw-text-center border-dashed tw-cursor-pointer"
                                    @click="(e) => startAttachingMedias(e)"
                                >
                                    <v-icon
                                        color="primary"
                                        size="60"
                                    >
                                        add
                                    </v-icon>
                                    <v-card-actions class="tw-justify-center">
                                        {{
                                            !files.length
                                                ? $t('order.labels.add-files')
                                                : $t('order.labels.add-more')
                                        }}
                                    </v-card-actions>
                                </v-card>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-container>
            </v-card-text>

            <v-card-actions class="px-3">
                <v-spacer />
                <SubmitButton
                    :is-processing="form.processing"
                    @click="submit"
                >
                    {{ $t('buttons.done') }}
                </SubmitButton>
            </v-card-actions>
        </v-card>
        <FileAttachmentFormMenu
            ref="fileAttachmentFormMenu"
            :allowed-types="$page.props.allowedTypes"
            @attach:from-file="(file) => attachFromMedia(file)"
            @attach:from-media="openMediaDrawer"
        />

        <MediaDrawer
            ref="mediaDrawerRef"
            :allowed-types="['images', 'videos', 'documents']"
            :categories="categories"
            right
            absolute
            width="320"
            style="z-index: 10"
            @attach="(media) => attachFromMedia(media)"
            @preview="(media) => $emit('click:view-media', media)"
        />
    </v-dialog>
</template>

<script>
import AddressAutocomplete from '@/Apps/Shared/Components/AutoComplete/AddressAutocomplete.vue'
import SubmitButton from '@/Components/Forms/SubmitButton.vue'
import AddressFields from '@components/Fields/AddressFields.vue'
import FileAttachmentFormMenu from '@/Components/Fields/File/FileAttachmentFormMenu.vue'
import MediaDrawer from '@/Components/Media/MediaDrawer.vue'

export default {
    components: {
        AddressAutocomplete,
        AddressFields,
        SubmitButton,
        FileAttachmentFormMenu,
        MediaDrawer,
    },

    data: (vm) => ({
        isShow: false,
        order: null,
        form: vm.$inertia.form({
            address: {
                house_number: null,
                street: null,
                city: null,
                state: null,
                country: null,
                zipcode: null,
            },
            category: null,
            medias: [],
        }),
        files: [],
        component: {},
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

        categories() {
            return this.$page.props.categories
        },

        alreadyAttachedMedias() {
            return !!this.files.length
        },
    },

    watch: {
        'form.address': function (newAddress) {
            if (!newAddress?.city && !newAddress?.city) return

            this.submit()
        },
    },

    methods: {
        show(order) {
            const deliveryAddress = order?.delivery_address

            if (!order || !deliveryAddress) return

            this.isShow = true

            this.$nextTick(() => {
                this.order = order
                this.form.address = deliveryAddress
            })
        },

        close() {
            this.$inertia.reload({
                only: ['errors', 'medias', 'flash'],
                preserveState: false,
            })
            this.isShow = false
            this.isShowAddressForm = false
            // this.order = null
            this.files = []
            this.form.reset()

            this.component = {}
        },

        submit() {
            if (!this.form.medias.length) return

            const order = this.order
            this.form.post(
                this.$route('orders.show.files.medias.store', {
                    order,
                }),
                {
                    only: ['errors', 'medias', 'flash'],
                    preserveScroll: true,
                    preserveState: false,
                },
            )
        },

        attachFromMedia(media) {
            this.form.medias.push(media.uuid)
            this.files.push(media)
        },

        showAddressForm() {
            this.form.address = {
                house_number: null,
                street: null,
                city: null,
                state: null,
                country: null,
                zipcode: null,
            }
            this.isShowAddressForm = true
        },

        saveAndSelectAddress() {
            const { id, ...address } = this.form.address

            this.$inertia.post(
                this.$route('addressbooks.addresses.store'),
                {
                    address: {
                        ...address,
                        country: address.country?.code,
                    },
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['flash_data'],
                    onSuccess: ({ props: { flash_data } }) => {
                        this.form.address = flash_data
                    },
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                    onFinish: () => (this.isShowAddressForm = false),
                },
            )
        },

        openMediaDrawer() {
            this.$inertia.reload({
                only: ['categories'],
                onSuccess: () => this.$refs.mediaDrawerRef.show(),
            })
        },

        startAttachingMedias(e) {
            if (this.alreadyAttachedMedias || this.form.category) {
                return this.$refs.fileAttachmentFormMenu.show(e)
            }

            this.$confirm.ask({
                title: this.$t('labels.warning'),
                question: this.$t(
                    'order.notifications.order-media.warning-no-category',
                ),
                type: 'warning',
                confirm: () => {
                    this.$refs.fileAttachmentFormMenu.show(e)
                },
            })
        },
    },
}
</script>
<style scoped>
.border-dashed {
    border: 2px dashed #37474f;
}
</style>
