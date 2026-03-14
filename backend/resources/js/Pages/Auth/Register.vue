<!-- eslint-disable vue/no-v-text-v-html-on-component -->
<template>
    <v-layout
        align-center
        justify-center
        column
        fill-height
    >
        <h1 class="display-3 font-weight-bold">
            <img
                :src="require('@/Images/logo.png')"
                :alt="$page.props.appName"
                class="tw-w-24 tw-h-24"
            />
        </h1>
        <p>{{ $t('headings.create-an-account') }}</p>

        <v-card
            tw-w-full
            class="sm:tw-max-w-[430px]"
        >
            <v-card-title
                v-if="hasFlash"
                primary-title
            >
                <v-alert
                    :type="flash?.type"
                    :value="true"
                    class="tw-w-full"
                >
                    {{ flash?.message }}
                </v-alert>
            </v-card-title>
            <v-card-text>
                <v-layout column>
                    <v-flex>
                        <v-layout
                            tw-flex-wrap
                            sm:tw-flex-nowrap
                        >
                            <v-flex sm6>
                                <input-text-field
                                    v-model="form.first_name"
                                    :label="$t('labels.inputs.firstname')"
                                    :hide-details="!form.errors.first_name"
                                    :error-messages="form.errors.first_name"
                                />
                            </v-flex>

                            <v-flex sm6>
                                <input-text-field
                                    v-model="form.last_name"
                                    :label="$t('labels.inputs.lastname')"
                                    :hide-details="!form.errors.last_name"
                                    :error-messages="form.errors.last_name"
                                />
                            </v-flex>
                        </v-layout>
                    </v-flex>

                    <v-flex>
                        <input-text-field
                            v-model="form.email"
                            :label="$t('labels.inputs.email')"
                            :hide-details="!form.errors.email"
                            :error-messages="form.errors.email"
                            :disabled="redirectedFromInvitation"
                        />
                    </v-flex>

                    <v-flex>
                        <input-text-field
                            v-model="form.password"
                            :label="$t('labels.inputs.password')"
                            type="password"
                            :hide-details="!form.errors.password"
                            :error-messages="form.errors.password"
                        />
                    </v-flex>

                    <v-flex>
                        <input-text-field
                            v-model="form.password_confirmation"
                            :label="$t('labels.inputs.confirm-password')"
                            type="password"
                            hide-details
                        />
                    </v-flex>

                    <v-flex>
                        <v-checkbox
                            v-model="form.agreement"
                            :error="form.errors.agreement"
                            class="ma-0"
                            hide-details
                            single-line
                        >
                            <template #label>
                                <span class="tw-text-xs sm:tw-text-base">
                                    {{ $t('messages.hereby-agree') }}&nbsp;
                                    <v-dialog
                                        v-model="showingTermsConditions"
                                        max-width="450"
                                        persistent
                                    >
                                        <template #activator="{ on }">
                                            <span
                                                class="blue--text"
                                                v-on="on"
                                            >
                                                {{
                                                    $t(
                                                        'labels.terms-conditions',
                                                    )
                                                }}
                                            </span>
                                        </template>

                                        <v-card>
                                            <v-layout>
                                                <v-card-title
                                                    class="mr-auto title"
                                                >
                                                    {{
                                                        $t(
                                                            'labels.terms-conditions',
                                                        )
                                                    }}
                                                </v-card-title>
                                                <v-btn
                                                    flat
                                                    shrink
                                                    icon
                                                    @click="
                                                        showingTermsConditions = false
                                                    "
                                                >
                                                    <v-icon>close</v-icon>
                                                </v-btn>
                                            </v-layout>
                                            <v-card-text
                                                style="
                                                    white-space: break-spaces;
                                                "
                                                v-html="
                                                    $page.props.termsConditions
                                                "
                                            />
                                        </v-card>
                                    </v-dialog>
                                </span>
                            </template>
                        </v-checkbox>
                    </v-flex>

                    <v-flex>
                        <v-btn
                            block
                            color="primary"
                            :loading="form.processing"
                            :disabled="form.processing"
                            @click="create"
                        >
                            {{ $t('buttons.create-account') }}
                            <template #loader>
                                <span>Registering...</span>
                            </template>
                        </v-btn>
                    </v-flex>
                </v-layout>

                <v-divider class="my-3" />

                <v-btn
                    :href="$route('auth.login')"
                    :disabled="form.processing"
                    depressed
                    block
                >
                    {{ $t('buttons.back-to-login') }}
                </v-btn>
            </v-card-text>
        </v-card>

        <locale-switcher
            class="mt-3"
            :lang="$page.props.locale"
            :languages="$page.props.locales"
        />
    </v-layout>
</template>

<script>
import HasFlashMessage from '@/Mixins/HasFlashMessage'
import InputTextField from '../../Components/InputTextField.vue'
import LocaleSwitcher from '../../Components/LocaleSwitcher.vue'
import GuestLayout from '../../Layouts/GuestLayout.vue'

export default {
    components: {
        InputTextField,
        LocaleSwitcher,
    },

    mixins: [HasFlashMessage],

    layout: GuestLayout,

    props: {
        alert: {
            type: Object,
            required: false,
            default: null,
        },
        metadata: {
            required: false,
            type: Object,
        },
    },

    data: (vm) => ({
        showingTermsConditions: false,

        form: vm.$inertia.form({
            first_name: null,
            last_name: null,
            email: null,
            password: null,
            password_confirmation: null,
            agreement: false,
            metadata: null,
        }),

        redirectedFromInvitation: false,
    }),

    mounted() {
        if (this.metadata) {
            this.redirectedFromInvitation = true

            this.form.email = this.metadata.email
            this.form.first_name = this.metadata.first_name
            this.form.last_name = this.metadata.last_name
            this.form.metadata = this.metadata.metadata
        }
    },

    methods: {
        create() {
            this.form.clearErrors()

            this.form.post(
                this.$route('auth.register.store'),
                {
                    preserveScroll: true,
                },
                {
                    resetOnSuccess: false,
                },
            )
        },
    },
}
</script>
