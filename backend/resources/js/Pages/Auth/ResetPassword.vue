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
        <p class="tw-text-center tw-px-2">{{ $t('hints.new-password') }}</p>

        <v-card class="tw-w-full sm:tw-w-[430px]">
            <v-card-text>
                <v-layout column>
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
                        <v-btn
                            block
                            color="primary"
                            :loading="form.processing"
                            :disabled="form.processing"
                            @click="reset"
                        >
                            {{ $t('buttons.reset') }}
                            <template #loader>
                                <span>Resetting...</span>
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
import LocaleSwitcher from '../../Components/LocaleSwitcher.vue'
import InputTextField from '../../Components/InputTextField.vue'
import GuestLayout from '../../Layouts/GuestLayout.vue'

export default {
    components: {
        InputTextField,
        LocaleSwitcher,
    },

    layout: GuestLayout,

    data: (vm) => ({
        form: vm.$inertia.form({
            token: vm.$page.props.token,
            email: vm.$page.props.email,
            password: null,
            password_confirmation: null,
        }),
    }),

    methods: {
        reset() {
            this.form.clearErrors()
            this.form.post(this.$route('auth.password.update'))
        },
    },
}
</script>
