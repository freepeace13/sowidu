<template>
    <form @submit.prevent="update">
        <v-layout
            row
            wrap
        >
            <v-flex
                v-if="$page.props.user.can['change avatar']"
                align-self-center
                xs12
                class="tw-flex tw-justify-center"
            >
                <v-avatar size="150">
                    <input-avatar
                        class="mx-auto"
                        :image="form.avatar"
                        @saved="selectAvatar"
                    />
                </v-avatar>
            </v-flex>

            <v-flex xs12>
                <input-text-field
                    v-model="form.name"
                    :label="$t('labels.inputs.name')"
                    :error-messages="form.errors.name"
                    :hide-details="!form.errors.name"
                    required
                />
            </v-flex>
            <v-flex
                xs6
                d-flex
                class="tw-gap-x-theme-2"
            >
                <v-select
                    v-model="form.legal_form"
                    :items="legalForms"
                    :label="$t('labels.inputs.legal-form')"
                    :error-messages="form.errors.legal_form"
                    :hide-details="!form.errors.legal_form"
                    item-text="name"
                    item-value="id"
                    outline
                />
            </v-flex>
            <v-flex xs6>
                <v-select
                    v-model="form.institution_type"
                    :items="institutionTypes"
                    :label="$t('labels.inputs.institution-type')"
                    :hide-details="!form.errors.institution_type"
                    :error-messages="form.errors.institution_type"
                    item-text="name"
                    item-value="id"
                    outline
                />
            </v-flex>
            <v-flex>
                <v-layout
                    tw-flex-wrap
                    sm:tw-flex-nowrap
                >
                    <v-flex xs12>
                        <AddressFields
                            v-model="form.address"
                            :is-loading="form.process"
                            :errors="form.errors"
                        />
                    </v-flex>
                </v-layout>
            </v-flex>
            <!-- <v-flex xs12>
                <v-layout
                    row
                    wrap
                >
                    <v-flex xs12>
                        <v-subheader>
                            {{ $t('account.labels.invoice-defaults') }}
                        </v-subheader>
                        <v-divider />
                    </v-flex>
                    <v-flex
                        xs12
                        sm6
                    >
                        <input-text-field
                            v-model="form.bank_name"
                            :label="$t('account.inputs.bank-name')"
                            :error-messages="form.errors.bank_name"
                            :hide-details="!form.errors.bank_name"
                            :loading="form.processing"
                            :disabled="form.processing"
                        />
                    </v-flex>
                    <v-flex
                        xs12
                        sm6
                    >
                        <v-select
                            v-model="form.currency"
                            outline
                            full-width
                            :items="currencies"
                            :loading="form.processing"
                            :error-messages="form?.errors.currency"
                            :hide-details="!form?.errors.currency"
                            :label="$t('labels.inputs.currency')"
                            solo
                            class="required-input"
                            required
                        />
                    </v-flex>
                    <v-flex
                        xs12
                        sm6
                    >
                        <input-text-field
                            v-model="form.iban"
                            :label="$t('account.inputs.iban')"
                            :error-messages="form.errors.iban"
                            :hide-details="!form.errors.iban"
                            :loading="form.processing"
                            :disabled="form.processing"
                        />
                    </v-flex>
                    <v-flex
                        xs12
                        sm6
                    >
                        <input-text-field
                            v-model="form.bic"
                            :label="$t('account.inputs.bic')"
                            :error-messages="form.errors.bic"
                            :hide-details="!form.errors.bic"
                            :loading="form.processing"
                            :disabled="form.processing"
                        />
                    </v-flex>

                    <v-flex
                        xs12
                        sm6
                    >
                        <v-select
                            v-model="form.commercial_register"
                            outline
                            full-width
                            :items="commercialRegisterTypes"
                            :loading="form.processing"
                            :error-messages="form?.errors.commercial_register"
                            :hide-details="!form?.errors.commercial_register"
                            :label="$t('account.inputs.commercial-register')"
                        />
                    </v-flex>
                    <v-flex
                        xs12
                        sm6
                    >
                        <input-text-field
                            v-model="form.commercial_register_number"
                            :label="
                                $t('account.inputs.commercial-register-number')
                            "
                            :error-messages="
                                form.errors.commercial_register_number
                            "
                            :hide-details="
                                !form.errors.commercial_register_number
                            "
                            :loading="form.processing"
                            :disabled="form.processing"
                        />
                    </v-flex>

                    <v-flex
                        xs12
                        sm6
                    >
                        <v-select
                            v-model="form.managing_director"
                            outline
                            full-width
                            :items="employees"
                            :loading="form.processing"
                            :error-messages="form?.errors.managing_director"
                            :hide-details="!form?.errors.managing_director"
                            :label="$t('account.inputs.managing-director')"
                            clearable
                            item-value="id"
                        >
                            <template #item="{ item }">
                                <slot
                                    name="item"
                                    v-bind="item"
                                >
                                    <v-list-tile-avatar>
                                        <img :src="item.photo" />
                                    </v-list-tile-avatar>
                                    <v-list-tile-content>
                                        <v-list-tile-title
                                            class="tw-flex tw-items-end tw-justify-between"
                                        >
                                            <div class="">
                                                {{ item.name }}
                                            </div>
                                        </v-list-tile-title>
                                        <v-list-tile-sub-title
                                            class="!tw-text-xs tw-font-thin"
                                        >
                                            {{ item.roles.join(', ') }}
                                        </v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </slot>
                            </template>
                            <template #selection="{ item }">
                                <v-list-tile-content>
                                    <v-list-tile-title
                                        class="tw-flex tw-items-end tw-justify-between"
                                    >
                                        <div class="">
                                            {{ item.name }}
                                        </div>
                                    </v-list-tile-title>
                                </v-list-tile-content>
                            </template>
                        </v-select>
                    </v-flex>
                    <v-flex
                        v-show="$vuetify.breakpoint.smAndUp"
                        xs12
                        sm6
                    />
                    <v-flex
                        sm6
                        xs12
                    >
                        <input-text-field
                            v-model="form.website"
                            :label="$t('account.inputs.website')"
                            :error-messages="form.errors.website"
                            :hide-details="!form.errors.website"
                            :loading="form.processing"
                            :disabled="form.processing"
                        />
                    </v-flex>
                    <v-flex
                        sm6
                        xs12
                    >
                        <input-text-field
                            v-model="form.company_email"
                            :label="$t('account.inputs.company-email')"
                            :error-messages="form.errors.company_email"
                            :hide-details="!form.errors.company_email"
                            :loading="form.processing"
                            :disabled="form.processing"
                        />
                    </v-flex>
                </v-layout>
            </v-flex> -->
            <v-flex mt-3>
                <v-layout>
                    <v-flex
                        pa-0
                        xs12
                        align-self-center
                        class="tw-flex tw-justify-end"
                    >
                        <SubmitButton
                            :is-processing="form.processing"
                            @click.native.stop="update"
                        >
                            {{ $t('buttons.save-changes') }}
                        </SubmitButton>
                    </v-flex>
                </v-layout>
            </v-flex>
        </v-layout>
    </form>
</template>

<script>
import SubmitButton from '@/Components/Forms/SubmitButton.vue'
import UpdatesProfiles from '@/Mixins/UpdatesProfiles'
import AddressFields from '@components/Fields/AddressFields.vue'

export default {
    name: 'TeamInformationForm',

    components: {
        AddressFields,
        SubmitButton,
    },

    mixins: [UpdatesProfiles],

    props: {
        user: {
            type: Object,
            default: () => ({}),
        },
        profile: {
            type: Object,
            required: true,
        },
        legalForms: {
            required: true,
            type: Array,
        },
        institutionTypes: {
            required: true,
            type: Array,
        },
        currencies: {
            required: false,
            type: Array,
            default: () => [],
        },
        employees: {
            required: false,
            type: Array,
            default: () => [],
        },
    },

    data: (vm) => ({
        form: vm.$inertia.form({
            avatar: vm.profile.photo,
            name: vm.profile.name,
            legal_form: vm.profile.legal_form?.id,
            institution_type: vm.profile.institution_type?.id,
            currency: vm.profile.currency?.name,
            address: {
                house_number: vm.profile?.address.house_number,
                street: vm.profile?.address.street,
                zipcode: vm.profile?.address.zipcode,
                city: vm.profile?.address.city,
                state: vm.profile?.address.state,
                country: vm.profile?.address.country,
            },
            // bank_name: vm.profile?.invoice_defaults?.bank_name,
            // iban: vm.profile?.invoice_defaults?.iban,
            // bic: vm.profile?.invoice_defaults?.bic,
            // managing_director: vm.profile?.invoice_defaults?.managing_director,
            // website: vm.profile?.invoice_defaults?.website,
            // company_email: vm.profile?.invoice_defaults?.company_email,
            // commercial_register:
            //     vm.profile?.invoice_defaults?.commercial_register,
            // commercial_register_number:
            //     vm.profile?.invoice_defaults?.commercial_register_number,
        }),
    }),

    computed: {
        commercialRegisterTypes() {
            return [
                {
                    value: 'HRB',
                    text: this.$t('account.inputs.commercial-register-hrb'),
                },
                {
                    value: 'HRA',
                    text: this.$t('account.inputs.commercial-register-hra'),
                },
            ]
        },
    },

    mounted() {
        this.$inertia.reload({ only: ['employees'] })
    },

    methods: {
        update() {
            this.form.clearErrors()

            this.form
                .transform((data) => ({
                    ...data,
                    _method: 'put',
                    address: {
                        ...data.address,
                        country: data.address.country?.code,
                    },
                }))
                .post(this.$route('account.profile.update'), {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['errors', 'profile'],
                    onSuccess: () => {
                        this.$root.$emit(
                            'flash.success',
                            this.$t('account.messages.organization.updated'),
                        )
                    },
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                })
        },
    },
}
</script>
