<script setup>
import { useForm } from '@inertiajs/vue2'
import useGlobalVariables from '@/Composables/useGlobalVariables'

const { $route } = useGlobalVariables()

const props = defineProps({
    account: {
        type: Object,
        required: false,
    },
})

const form = useForm({
    first_name: props.account.firstName,
    last_name: props.account.lastName,
    password: null,
    password_confirmation: null,
})

function proceed() {
    form.put($route('account.update'))
}
</script>

<template>
    <v-app>
        <v-content>
            <v-container
                fluid
                grid-list-lg
            >
                <v-layout
                    items-center
                    justify-center
                >
                    <v-card width="550">
                        <v-card-title class="font-weight-bold title">
                            {{ $t('hints.set-your-password') }}
                        </v-card-title>

                        <v-card-text>
                            <v-layout>
                                <v-flex>
                                    <v-text-field
                                        v-model="form.first_name"
                                        :label="$t('labels.inputs.firstname')"
                                        :hide-details="!form.errors.first_name"
                                        :error-messages="form.errors.first_name"
                                        placeholder=" "
                                        outline
                                    />
                                </v-flex>
                                <v-flex>
                                    <v-text-field
                                        v-model="form.last_name"
                                        :label="$t('labels.inputs.lastname')"
                                        :hide-details="!form.errors.last_name"
                                        :error-messages="form.errors.last_name"
                                        placeholder=" "
                                        outline
                                    />
                                </v-flex>
                            </v-layout>

                            <v-layout column>
                                <v-flex>
                                    <v-text-field
                                        v-model="form.password"
                                        type="password"
                                        :label="$t('labels.inputs.password')"
                                        :error-messages="form.errors.password"
                                        :hide-details="!form.errors.password"
                                        placeholder=" "
                                        outline
                                    />
                                </v-flex>

                                <v-flex>
                                    <v-text-field
                                        v-model="form.password_confirmation"
                                        type="password"
                                        :label="
                                            $t('labels.inputs.confirm-password')
                                        "
                                        hide-details
                                        placeholder=" "
                                        outline
                                    />
                                </v-flex>
                            </v-layout>
                        </v-card-text>

                        <v-card-actions>
                            <v-spacer />
                            <v-btn
                                color="primary"
                                @click="proceed"
                            >
                                {{ $t('buttons.proceed') }}
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-layout>
            </v-container>
        </v-content>
    </v-app>
</template>
