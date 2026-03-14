<script setup>
import { computed } from 'vue'
import AddressFields from '@components/Fields/AddressFields.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'

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

const client = computed(() => props.value.client)
const address = computed(() => client.value.address)
</script>
<template>
    <div>
        <v-layout
            row
            wrap
            px-2
        >
            <v-flex
                sm6
                xs12
            >
                <v-text-field
                    v-model="client.first_name"
                    color="primary"
                    :loading="value.processing"
                    :error-messages="value.errors.first_name"
                    label="First Name"
                    autofocus
                    outline
                    :hide-details="!value.errors.first_name"
                    required
                    class="required-input"
                />
            </v-flex>
            <v-flex
                sm6
                xs12
            >
                <v-text-field
                    v-model="client.last_name"
                    color="primary"
                    :loading="value.processing"
                    :error-messages="value.errors.last_name"
                    label="Last Name"
                    autofocus
                    outline
                    :hide-details="!value.errors.last_name"
                    required
                    class="required-input"
                />
            </v-flex>
        </v-layout>
        <v-flex>
            <v-text-field
                v-model="client.email"
                color="primary"
                :loading="value.processing"
                :error-messages="value.errors.email"
                label="Email"
                autofocus
                outline
                :hide-details="!value.errors.email"
                required
                class="required-input"
            />
        </v-flex>
        <v-flex>
            <v-text-field
                v-model="client.phone"
                color="primary"
                :loading="value.processing"
                :error-messages="value.errors.phone"
                label="Phone"
                autofocus
                outline
                :hide-details="!value.errors.phone"
                required
            />
        </v-flex>
        <v-expansion-panel
            px-0
            class="tw-shadow-none"
            :value="0"
        >
            <v-expansion-panel-content>
                <template #header>
                    <v-subheader class="tw-cursor-pointer info--text px-0">
                        {{ $t('order.labels.address-information') }}
                    </v-subheader>
                </template>
                <v-layout column>
                    <v-flex
                        xs12
                        px-3
                    >
                        <AddressFields v-model="address" />
                    </v-flex>
                </v-layout>
            </v-expansion-panel-content>
        </v-expansion-panel>
    </div>
</template>
