<script setup>
import AppAvatar from '@components/AppAvatar.vue'
import { computed } from 'vue'

const props = defineProps({
    value: {
        required: true,
        type: Object,
    },
    isEditing: {
        required: false,
        type: Boolean,
        default: false,
    },
})

const address = computed(() => props.value.contractor.address)
</script>
<template>
    <div>
        <v-layout
            row
            wrap
            px-2
        >
            <v-flex xs12>
                <h5 class="tw-text-lg">
                    {{ $t('order.labels.contractor-details') }}
                </h5>
            </v-flex>
            <v-flex
                xs12
                pt-0
            >
                <v-card
                    color="grey lighten-4"
                    flat
                >
                    <v-card-text class="pa-2">
                        <v-layout
                            row
                            wrap
                            px-2
                        >
                            <v-flex
                                sm6
                                xs12
                            >
                                <VLabel>
                                    {{ $t('labels.inputs.name') }}:
                                    <span
                                        class="tw-font-bold"
                                        v-text="value.contractor?.name"
                                    />
                                </VLabel>
                            </v-flex>
                            <v-flex
                                sm6
                                xs12
                            >
                                <VLabel>
                                    {{ $t('labels.inputs.legal-form') }}:
                                    <span
                                        class="tw-font-bold"
                                        v-text="value.contractor?.legalform"
                                    />
                                </VLabel>
                            </v-flex>
                            <v-flex
                                sm6
                                xs12
                            >
                                <VLabel>
                                    {{ $t('labels.inputs.institution-type') }}:
                                    <span
                                        class="tw-font-bold"
                                        v-text="
                                            value.contractor?.institution_type
                                        "
                                    />
                                </VLabel>
                            </v-flex>
                            <v-flex
                                sm6
                                xs12
                            >
                                <VLabel>
                                    {{ $t('order.labels.phone') }}:
                                    <span
                                        :class="[
                                            'tw-font-bold',
                                            {
                                                'tw-italic tw-font-thin':
                                                    !value.contractor?.phone,
                                            },
                                        ]"
                                        v-text="
                                            value.contractor?.phone ?? 'None'
                                        "
                                    />
                                </VLabel>
                            </v-flex>
                            <v-flex
                                xs12
                                class="tw-flex"
                            >
                                <VLabel> Company Logo: </VLabel>
                                <AppAvatar
                                    :avatar="value.contractor?.photo"
                                    :name="value.contractor?.name"
                                    :size="50"
                                    class="tw-h-14 tw-w-14 tw-ml-2"
                                />
                            </v-flex>
                        </v-layout>
                        <v-expansion-panel
                            px-0
                            class="tw-shadow-none"
                        >
                            <v-expansion-panel-content pl-0>
                                <template #header>
                                    <div
                                        class="tw-text-primary tw-font-bold !tw-text-sm mb-3"
                                    >
                                        {{
                                            $t(
                                                'order.labels.address-information',
                                            )
                                        }}
                                    </div>
                                </template>
                                <v-layout
                                    row
                                    wrap
                                    px-3
                                    mb-2
                                >
                                    <v-flex
                                        xs12
                                        sm6
                                    >
                                        <VLabel>
                                            {{ $t('labels.inputs.house-no') }}:
                                            <span
                                                class="tw-font-bold"
                                                v-text="address.house_number"
                                            />
                                        </VLabel>
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        sm6
                                    >
                                        <VLabel>
                                            {{ $t('labels.inputs.street') }}:
                                            <span
                                                class="tw-font-bold"
                                                v-text="address.street"
                                            />
                                        </VLabel>
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        sm6
                                    >
                                        <VLabel>
                                            {{ $t('labels.inputs.zipcode') }}:
                                            <span
                                                class="tw-font-bold"
                                                v-text="address.zipcode"
                                            />
                                        </VLabel>
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        sm6
                                    >
                                        <VLabel>
                                            {{ $t('labels.inputs.city') }}:
                                            <span
                                                class="tw-font-bold"
                                                v-text="address.city"
                                            />
                                        </VLabel>
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        sm6
                                    >
                                        <VLabel>
                                            {{ $t('labels.inputs.state') }}:
                                            <span
                                                class="tw-font-bold"
                                                v-text="address.state"
                                            />
                                        </VLabel>
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        sm6
                                    >
                                        <VLabel>
                                            {{ $t('labels.inputs.country') }}:
                                            <span
                                                class="tw-font-bold"
                                                v-text="address.country?.name"
                                            />
                                        </VLabel>
                                    </v-flex>
                                </v-layout>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </div>
</template>
