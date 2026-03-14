<script setup>
import AppAvatar from '@/Components/AppAvatar.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'

defineProps({
    client: {
        required: true,
        type: Object,
    },
})

const { $t } = useGlobalVariables()
</script>
<template>
    <v-layout
        row
        wrap
        px-2
    >
        <v-flex xs12>
            <h5 class="tw-text-lg">
                {{ $t('order.labels.client-information') }}
            </h5>
        </v-flex>
        <v-flex xs12>
            <VLabel>
                {{ $t('labels.inputs.name') }}:
                <span
                    class="tw-font-bold"
                    v-text="client?.name"
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
                    v-text="client?.legalform"
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
                    v-text="client?.institution_type"
                />
            </VLabel>
        </v-flex>
        <v-flex
            xs12
            class="tw-flex"
        >
            <VLabel> {{ $t('labels.inputs.photo') }}: </VLabel>
            <AppAvatar
                :avatar="client?.photo"
                :name="client?.name"
                :size="50"
                class="tw-h-14 tw-w-14 tw-ml-2"
            />
        </v-flex>
    </v-layout>
</template>
