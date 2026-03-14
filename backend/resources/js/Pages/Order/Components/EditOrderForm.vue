<template>
    <v-dialog
        v-model="isShow"
        persistent
        max-width="600px"
        lazy
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ $t('order.labels.update-order') }}
                </v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="close"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-divider />
            <v-card-text class="pt-0">
                <v-container
                    grid-list-lg
                    fluid
                    pa-2
                >
                    <v-layout column>
                        <v-flex>
                            <v-subheader
                                px-0
                                class="subheading tw-text-primary tw-font-bold"
                            >
                                {{ $t('order.labels.order-details') }}
                            </v-subheader>
                        </v-flex>
                        <v-layout
                            row
                            wrap
                            px-2
                        >
                            <v-flex
                                sm6
                                offset-sm6
                                xs12
                            >
                                <v-menu
                                    :close-on-content-click="false"
                                    lazy
                                    transition="scale-transition"
                                    offset-y
                                    full-width
                                    min-width="290px"
                                >
                                    <template #activator="{ on }">
                                        <v-text-field
                                            :value="form.order_date"
                                            :loading="form.processing"
                                            :disabled="form.processing"
                                            :error-messages="
                                                form.errors.order_date
                                            "
                                            :hide-details="
                                                !form.errors.order_date
                                            "
                                            :label="
                                                $t(
                                                    'order.labels.inputs.order-date',
                                                )
                                            "
                                            required
                                            readonly
                                            color="primary"
                                            outline
                                            class="required-input"
                                            v-on="on"
                                        />
                                    </template>
                                    <v-date-picker
                                        v-model="form.order_date"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        scrollable
                                        reactive
                                        picker-date
                                    />
                                </v-menu>
                            </v-flex>
                            <v-flex
                                sm6
                                xs12
                            >
                                <v-menu
                                    :close-on-content-click="false"
                                    lazy
                                    transition="scale-transition"
                                    offset-y
                                    full-width
                                    min-width="290px"
                                >
                                    <template #activator="{ on }">
                                        <v-text-field
                                            :value="form.planned_start_date"
                                            :loading="form.processing"
                                            :disabled="form.processing"
                                            :error-messages="
                                                form.errors.planned_start_date
                                            "
                                            :hide-details="
                                                !form.errors.planned_start_date
                                            "
                                            :label="
                                                $t(
                                                    'order.labels.inputs.planned-start-date',
                                                )
                                            "
                                            required
                                            readonly
                                            color="primary"
                                            outline
                                            v-on="on"
                                        />
                                    </template>
                                    <v-date-picker
                                        v-model="form.planned_start_date"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        scrollable
                                        reactive
                                        picker-date
                                    />
                                </v-menu>
                            </v-flex>
                            <v-flex
                                sm6
                                xs12
                            >
                                <v-menu
                                    :close-on-content-click="false"
                                    lazy
                                    transition="scale-transition"
                                    offset-y
                                    full-width
                                    min-width="290px"
                                >
                                    <template #activator="{ on }">
                                        <v-text-field
                                            :value="form.planned_finish_date"
                                            :loading="form.processing"
                                            :disabled="form.processing"
                                            :error-messages="
                                                form.errors.planned_finish_date
                                            "
                                            :hide-details="
                                                !form.errors.planned_finish_date
                                            "
                                            :label="
                                                $t(
                                                    'order.labels.inputs.planned-finish-date',
                                                )
                                            "
                                            required
                                            readonly
                                            color="primary"
                                            outline
                                            v-on="on"
                                        />
                                    </template>
                                    <v-date-picker
                                        v-model="form.planned_finish_date"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        scrollable
                                        reactive
                                        picker-date
                                    />
                                </v-menu>
                            </v-flex>
                        </v-layout>

                        <v-flex xs12>
                            <v-textarea
                                v-model="form.description"
                                :loading="form.processing"
                                label="Order description"
                                :error-messages="form.errors.description"
                                :hide-details="!form.errors.description"
                                outline
                                class="required-input"
                            />
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-divider />
            <v-card-actions class="px-4">
                <v-btn
                    :disabled="form.processing"
                    outline
                    depressed
                    @click="close"
                >
                    Cancel
                </v-btn>
                <v-spacer />
                <SubmitButton
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="submit"
                >
                    {{ $t('buttons.update') }}
                    <template #loader>
                        <span>{{ $t('buttons.updating') }}...</span>
                    </template>
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import { useDateNow } from '@/Composables/useDayJs'
import SubmitButton from '@components/Forms/SubmitButton.vue'
import axios from 'axios'

export default {
    components: {
        SubmitButton,
    },

    props: {
        refreshProps: {
            type: Array,
            default: () => [],
            required: false,
        },
        route: {
            required: true,
            type: String,
        },
    },

    data: (vm) => ({
        form: vm.$inertia.form({
            id: null,
            description: '',
            order_date: vm.defaultDate(),
            planned_start_date: null,
            planned_finish_date: null,
        }),
        isShow: false,
        order: null,
    }),

    methods: {
        async show(order) {
            if (!order) return

            this.form.reset()

            try {
                const { data } = await axios.get(
                    this.$route('json.order.show', { order }),
                )

                if (!data) {
                    this.$inertia.reload({ only: ['flash'] })
                    return
                }

                this.setOrderInputs(data)

                this.isShow = true
            } catch (error) {
                this.$root.$emit('flash.error', error)
            }
        },

        setOrderInputs(order) {
            this.order = order

            this.form.id = order.id
            this.form.description = order.description
            this.form.order_date = order.order_date
            this.form.planned_start_date = order.planned_start_date
            this.form.planned_finish_date = order.planned_finish_date
        },

        close() {
            this.reset()
            this.order = null
            this.isShow = false
        },

        defaultDate() {
            return useDateNow()
        },

        reset() {
            this.form.reset()
            this.form.clearErrors()
        },

        setClient(client) {
            this.form.client = client
            this.form.delivery_address.id = client.address.id
        },

        submit() {
            const order = this.order
            const propsToRefresh = this.refreshProps.concat(['errors'])

            this.form.patch(
                this.$route(this.route, {
                    order,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: propsToRefresh,
                    onSuccess: () => {
                        this.$root.$emit(
                            'flash.success',
                            'Order has been updated.',
                        )
                        this.$emit('refresh')
                        this.close()
                    },
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                },
            )
        },

        deliveryAddressChanged(isSameAsAddress) {
            this.form.delivery_address = this.getDefaultAddress()
            if (isSameAsAddress) {
                // Delivery address is same as current address
                this.form.delivery_address.id = this.form.client.address.id
            }
        },
    },
}
</script>
<style scoped>
.v-expansion-panel__header {
    padding-left: 0px;
    padding-right: 0px;
}
</style>
