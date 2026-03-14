<script setup>
import { usePage } from '@inertiajs/vue2'
import { computed } from 'vue'
import useGlobalVariables from '~Shared/Composables/useGlobalVariables'

const { $t, $route } = useGlobalVariables()

const { invoice } = usePage().props

const client = computed(() => invoice.value.client)
const careOf = computed(() =>
    invoice.value.care_of_address && invoice.value.care_of_name
        ? [
              invoice.value.care_of_name,
              invoice.value.care_of_legalform,
              invoice.value.care_of_address,
          ]
              .filter(Boolean)
              .join(' ')
        : null,
)
</script>

<template>
    <v-flex
        xs7
        class="tw-text-lg"
    >
        <div class="tw-font-semibold">{{ $t('invoices.labels.bill-to') }}:</div>

        <div>{{ client?.name }}</div>
        <div>
            <v-img
                :src="client?.photo"
                contain
                class="grey lighten-2 mr-2"
                height="50"
                width="50"
            />
        </div>
        <!-- eslint-disable vue/no-v-html -->
        <div v-html="careOf ?? client?.address.full" />
        <!-- eslint-enable vue/no-v-html -->

        <div>
            <v-label>
                {{ $t('labels.order-no') }}
            </v-label>
            <a
                :href="$route('orders.show', { order: invoice?.order?.id })"
                target="_blank"
                class="tw-text-info tw-cursor-pointer hover:tw-underline"
            >
                {{ invoice?.order.order_number }}
            </a>
        </div>
    </v-flex>
</template>
