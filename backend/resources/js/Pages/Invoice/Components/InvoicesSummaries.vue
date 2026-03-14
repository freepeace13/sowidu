<script setup>
import { useGetPageProps } from '@/Composables/useGetPageProps'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import axios from 'axios'
import { onMounted, ref } from 'vue'
import SummaryCard from './SummaryCard.vue'

defineExpose({ fetch })

const { $route } = useGlobalVariables()
// const url = ref($route('json.invoices.summaries'))
const isFetching = ref(true)

const data = ref(null)

async function fetch(filters = {}) {
    try {
        isFetching.value = true
        const response = await axios.get(
            $route('json.invoices.summaries', filters),
        )
        data.value = response.data
    } catch (error) {
        console.error(error)
    } finally {
        isFetching.value = false
    }
}

// watch(data, (value) => {
//     console.log('🚀 ~ watch ~ value:', value)
// })

onMounted(async () => {
    const defaultFilters = useGetPageProps('filters')
    fetch(defaultFilters)
})
</script>
<template>
    <v-layout
        row
        wrap
    >
        <v-flex
            xs12
            sm4
        >
            <SummaryCard
                :is-loading="isFetching"
                :label="$t('invoices.labels.total-without-vat')"
                :amount="data?.total_without_vat_formatted"
                color="blue lighten-4"
            />
        </v-flex>
        <v-flex
            xs12
            sm4
        >
            <SummaryCard
                :label="$t('invoices.labels.total-vat')"
                :amount="data?.total_vat_formatted"
                :is-loading="isFetching"
                color="purple lighten-4"
            />
        </v-flex>
        <v-flex
            xs12
            sm4
        >
            <SummaryCard
                :label="$t('invoices.labels.total-with-vat')"
                :amount="data?.total_with_vat_formatted"
                :is-loading="isFetching"
                color="teal lighten-4"
            />
        </v-flex>
    </v-layout>
</template>
