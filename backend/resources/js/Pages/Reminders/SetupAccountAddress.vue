<template>
    <v-container
        fluid
        grid-list-lg
        fill-height
        py-0
    >
        <v-layout
            align-center
            justify-center
            fill-height
            column
        >
            <h1 class="display-2 font-weight-bold mb-3 tw-text-center">
                {{ $page.props.name }}
            </h1>

            <v-card class="tw-w-full sm:tw-w-2/3 lg:tw-w-1/2">
                <v-card-text>
                    <form @submit.prevent="submitForm">
                        <v-layout
                            fill-height
                            column
                        >
                            <v-flex>
                                <HouseNumberField
                                    :house-number.sync="form.house_number"
                                    :hide-details="!form.errors.house_number"
                                    :error-messages="form.errors.house_number"
                                    :label="$t('labels.inputs.house-no')"
                                    outline
                                />
                            </v-flex>
                            <v-flex>
                                <StreetField
                                    :house-number.sync="form.street"
                                    :hide-details="!form.errors.street"
                                    :error-messages="form.errors.street"
                                    :label="$t('labels.inputs.street')"
                                    outline
                                />
                            </v-flex>
                            <v-flex>
                                <ZipcodeField
                                    :zipcode.sync="form.zipcode"
                                    :hide-details="!form.errors.zipcode"
                                    :error-messages="form.errors.zipcode"
                                    :label="$t('labels.inputs.zipcode')"
                                    outline
                                />
                            </v-flex>
                            <v-flex>
                                <v-layout
                                    tw-flex-wrap
                                    sm:tw-flex-nowrap
                                >
                                    <v-flex sm6>
                                        <StateField
                                            v-model="form.state"
                                            :country="form.country"
                                            :hide-details="!form.errors.state"
                                            :error-messages="form.errors.state"
                                            :label="$t('labels.inputs.state')"
                                            :placeholder="$t('hints.select-or-search-for-state')"
                                            outline
                                        />
                                    </v-flex>
                                    <v-flex sm6>
                                        <CityField
                                            v-model="form.city"
                                            :country="form.country"
                                            :hide-details="!form.errors.city"
                                            :error-messages="form.errors.city"
                                            :label="$t('labels.inputs.city')"
                                            :placeholder="
                                                $t(
                                                    'hints.select-or-search-for-city',
                                                )
                                            "
                                            outline
                                        />
                                    </v-flex>
                                </v-layout>
                            </v-flex>
                            <v-flex>
                                <CountryField
                                    v-model="form.country"
                                    :hide-details="!form.errors.country"
                                    :error-messages="form.errors.country"
                                    :placeholder="
                                        $t(
                                            'hints.start-typing-to-search-country',
                                        )
                                    "
                                    color="primary"
                                    :label="$t('labels.inputs.country')"
                                    outline
                                />
                            </v-flex>
                            <v-flex>
                                <v-btn
                                    block
                                    type="submit"
                                    color="primary"
                                    :loading="form.processing"
                                    :disabled="form.processing"
                                    @click="submitForm"
                                >
                                    {{ $t('buttons.update') }}
                                    <template #loader>
                                        <span>{{
                                            `${$t('buttons.updating')}...`
                                        }}</span>
                                    </template>
                                </v-btn>
                                <v-btn
                                    block
                                    flat
                                    color="blue"
                                    :disabled="form.processing"
                                    @click="skipReminder"
                                >
                                    {{ $t('buttons.remind-me-later') }}
                                </v-btn>
                            </v-flex>
                        </v-layout>
                    </form>
                </v-card-text>
            </v-card>
        </v-layout>
    </v-container>
</template>

<script>
import StreetField from '@/Apps/Shared/Containers/Fields/Places/StreetField.vue'
import ZipcodeField from '@/Apps/Shared/Containers/Fields/Places/ZipcodeField.vue'
import HouseNumberField from '@/Apps/Shared/Containers/Fields/Places/HouseNumberField.vue'
import StateField from '@/Apps/Shared/Containers/Fields/Places/StateField.vue'
import CityField from '@/Apps/Shared/Containers/Fields/Places/CityField.vue'
import CountryField from '@/Apps/Shared/Containers/Fields/Places/CountryField.vue'
import ReminderLayout from '@/Layouts/ReminderLayout.vue'

export default {
    components: {
        ZipcodeField,
        StreetField,
        HouseNumberField,
        CountryField,
        StateField,
        CityField,
    },

    layout: ReminderLayout,

    data: (vm) => ({
        person: null,
        org: null,
        form: vm.$inertia.form({
            house_number: null,
            street: null,
            city: null,
            state: null,
            country: null,
            zipcode: null,
        }),
    }),

    methods: {
        submitForm() {
            this.form.post(
                this.$route('reminders.submit', {
                    id: this.$page.props.id,
                }),
                {
                    onSuccess: console.log,
                    onError: console.log,
                },
            )
        },

        skipReminder() {
            this.$inertia.post(
                this.$route('reminders.skip', { id: this.$page.props.id }),
                { duration: this.$page.props.duration },
            )
        },
    },
}
</script>
