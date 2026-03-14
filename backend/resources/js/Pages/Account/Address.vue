<template>
    <div class="mb-5">
        <v-layout
            align-center
            justify-space-between
        >
            <v-flex class="headline shrink font-weight-bold">
                {{ $t('headings.address') }}
            </v-flex>
        </v-layout>

        <v-divider class="mb-3" />

        <v-card
            color="transparent"
            flat
            width="50%"
        >
            <v-layout column>
                <v-flex>
                    <input-text-field
                        v-model="form.street"
                        :label="$t('labels.inputs.street')"
                        :error-messages="form.errors.street"
                        :hide-details="!form.errors.street"
                    />
                </v-flex>

                <v-flex>
                    <input-text-field
                        v-model="form.city"
                        :label="$t('labels.inputs.city')"
                        :error-messages="form.errors.city"
                        :hide-details="!form.errors.city"
                    />
                </v-flex>

                <v-flex>
                    <v-layout>
                        <v-flex xs6>
                            <v-autocomplete
                                placeholder=" "
                                full-width
                                outline
                            >
                                <template #label>
                                    <input-label
                                        :label="$t('labels.inputs.state')"
                                        required
                                    />
                                </template>
                            </v-autocomplete>
                        </v-flex>

                        <v-flex xs6>
                            <input-select
                                v-model="form.country"
                                :label="$t('labels.inputs.country')"
                                :error-messages="form.errors.country"
                                :items="$page.props.countries"
                                :hide-details="!form.errors.countries"
                                required
                            />
                        </v-flex>
                    </v-layout>
                </v-flex>
            </v-layout>

            <v-btn
                color="primary"
                :loading="form.processing"
                :disabled="form.processing"
                @click="createAddress"
            >
                {{ $t('buttons.create') }}
                <template #loader>
                    <span>Creating...</span>
                </template>
            </v-btn>
        </v-card>
    </div>
</template>

<script>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import InputLabel from '@components/InputLabel.vue'
import InputSelect from '@components/InputSelect.vue'
import InputTextField from '@components/InputTextField.vue'
import AccountPageLayout from './AccountPageLayout.vue'

export default {
    components: {
        InputTextField,
        InputSelect,
        InputLabel,
    },

    layout: [AuthLayout, AccountPageLayout],

    data: (vm) => ({
        form: vm.$inertia.form({
            street: vm.$page.props.address.street,
            state: vm.$page.props.address.state,
            city: vm.$page.props.address.city,
            country: vm.$page.props.address.country,
        }),
    }),

    methods: {
        createAddress() {},
    },
}
</script>
