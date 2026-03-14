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
        <p>{{ $t('hints.please-login-into-your-account') }}</p>

        <v-card class="tw-w-full sm:tw-w-[380px]">
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
                    <div
                        v-show="flash?.show_resend_link"
                        class="tw-text-center tw-mt-1"
                    >
                        <v-btn
                            color="info"
                            small
                            @click="resendVerification"
                        >
                            {{ $t('buttons.resend-verification-link') }}
                        </v-btn>
                    </div>
                </v-alert>
            </v-card-title>
            <v-card-text>
                <form @submit.prevent="authenticate">
                    <v-layout column>
                        <v-flex>
                            <input-text-field
                                v-model="form.email"
                                :label="$t('labels.inputs.email')"
                                type="email"
                                :hide-details="!form.errors.email"
                                :error-messages="form.errors.email"
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

                        <v-flex class="text-xs-right">
                            <a :href="$route('auth.password.request')">
                                {{ $t('questions.auth.forgot-password') }}
                            </a>
                        </v-flex>

                        <v-flex>
                            <v-btn
                                block
                                type="submit"
                                color="primary"
                                :loading="form.processing"
                                :disabled="form.processing"
                                @click="authenticate"
                            >
                                {{ $t('buttons.login') }}
                                <template #loader>
                                    <span>{{
                                        `${$t('buttons.logging-in')}...`
                                    }}</span>
                                </template>
                            </v-btn>
                        </v-flex>
                    </v-layout>
                </form>

                <v-divider class="my-3" />

                <v-btn
                    :href="$route('auth.register')"
                    :disabled="form.processing"
                    depressed
                    block
                >
                    {{ $t('buttons.create-account') }}
                </v-btn>
            </v-card-text>
        </v-card>
        <div
            class="tw-flex tw-items-center mt-3 tw-w-full sm:tw-w-[380px] tw-justify-between"
        >
            <Link :href="$route('impressum')">Impressum</Link>
            <locale-switcher
                :lang="$page.props.locale"
                :languages="$page.props.locales"
            />
            <div class="tw-invisible">Impressum</div>
        </div>
    </v-layout>
</template>

<script>
import HasFlashMessage from '@/Mixins/HasFlashMessage'
import { Link } from '@inertiajs/vue2'
import InputTextField from '../../Components/InputTextField.vue'
import LocaleSwitcher from '../../Components/LocaleSwitcher.vue'
import GuestLayout from '../../Layouts/GuestLayout.vue'

export default {
    components: {
        InputTextField,
        LocaleSwitcher,
        // eslint-disable-next-line vue/no-reserved-component-names
        Link,
    },

    mixins: [HasFlashMessage],

    layout: GuestLayout,

    data: (vm) => ({
        form: vm.$inertia.form({
            email: null,
            password: null,
        }),
    }),

    methods: {
        authenticate() {
            this.form.clearErrors()

            this.form.post(this.$route('auth.login.store'), {
                onSuccess: (page) => console.log('success', page),
            })
        },

        resendVerification() {
            this.form
                .transform(({ password, ...rest }) => ({
                    ...rest,
                }))
                .post(this.$route('auth.verification.resend'), {
                    preserveState: true,
                    preserveScroll: true,
                })
        },
    },
}
</script>
