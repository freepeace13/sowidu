<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { computed } from 'vue'
import ClientCompany from './ClientCompany.vue'
import ClientPerson from './ClientPerson.vue'

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

const { $t } = useGlobalVariables()

const address = computed(() => props.value.client.address)
</script>
<template>
    <div>
        <ClientCompany
            v-if="value.client?.legalform"
            :client="value.client"
        />
        <ClientPerson
            v-else
            :client="value.client"
        />
        <v-expansion-panel
            px-0
            class="tw-shadow-none"
        >
            <v-expansion-panel-content pl-0>
                <template #header>
                    <div class="tw-text-primary tw-font-bold !tw-text-sm mb-3">
                        {{ $t('order.labels.address-information') }}
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
    </div>
</template>
