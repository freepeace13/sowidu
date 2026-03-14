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
        <p class="tw-text-center tw-px-2">
            {{ $t('hints.reset-password-link') }}
        </p>

        <v-card class="tw-w-full sm:tw-w-[430px]">
            <v-card-text>
                <v-layout column>
                    <v-flex>
                        <input-text-field
                            v-model="form.email"
                            :label="$t('labels.inputs.email')"
                            :hide-details="!form.errors.email"
                            :error-messages="form.errors.email"
                        />
                    </v-flex>

                    <v-flex>
                        <v-btn
                            block
                            color="primary"
                            :disabled="form.processing"
                            :loading="form.processing"
                            @click="sendResetLinkEmail"
                        >
                            {{ $t('buttons.proceed') }}
                            <template #loader>
                                <span>{{
                                    `${$t('buttons.processing')}...`
                                }}</span>
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
            email: null,
        }),
    }),

    methods: {
        sendResetLinkEmail() {
            this.form.clearErrors()

            this.form.post(this.$route('auth.password.email'), {
                onSuccess: () => this.form.reset(),
            })
        },
    },
}
</script>
