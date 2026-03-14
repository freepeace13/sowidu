<template>
    <main>
        <h1 class="text-xs-center mb-1 display-3 font-weight-bold">Sowidu</h1>
        <h2 class="text-xs-center mb-3">{{ $t('hints.enter-verification-code') }}</h2>

        <v-card class="mx-auto">
            <v-card-text class="pa-5">
                <web-form method="post">
                    <template v-slot="{ errors }">
                        <v-alert :value="true" type="success" class="mb-3">
                            {{ $t('alerts.auth.verification-sent') }}
                        </v-alert>

                        <TextField
                            name="pincode"
                            :errors="errors.get('pincode')"
                            placeholder="Enter 6-digit code here"
                            mask="NNNNNN"
                            :label="$t('labels.inputs.verification-code')"
                        ></TextField>

                        <v-btn type="submit" color="primary" large block>
                            {{ $t('buttons.continue') }}
                        </v-btn>

                        <v-divider class="my-3"></v-divider>
                    </template>
                </web-form>

                <p>{{ $t('hints.auth.resend-verification') }}</p>

                <web-form
                    :url="`/auth/${$route.params.token}/resend`"
                    method="post"
                >
                    <v-btn type="submit" large block>
                        {{ $t('buttons.resend-verification') }}
                    </v-btn>
                </web-form>
            </v-card-text>
        </v-card>

        <div class="text-xs-center mt-3">&copy; 2020 Sowidu. All rights reserved.</div>
    </main>
</template>

<script>
import WebForm from '~/components/containers/WebForm';

export default {
    layout: 'Guest',

    components: {
        WebForm
    }
}
</script>
