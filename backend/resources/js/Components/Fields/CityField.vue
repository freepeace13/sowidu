<script setup>
import { useGetCountryCities } from '@/Apps/Shared/Composables/usePlaceService'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { watchDebounced } from '@vueuse/core'
import { ref, watch } from 'vue'

const props = defineProps({
    value: {
        type: String,
        default: null,
    },
    countryCode: {
        type: [String, Number],
        default: null,
        required: false,
    },
})

const { $root } = useGlobalVariables()

const selected = ref(props.value)
const search = ref(null)
const items = ref([])
const isLoading = ref(false)

watch(
    () => props.value,
    (val) => {
        selected.value = val

        if (!val) return

        items.value = [val]
    },
)

watchDebounced(
    search,
    async (keyword) => {
        if (isLoading.value) return

        await fetchResults(keyword)
    },
    {
        debounce: 400,
    },
)

watchDebounced(
    () => props.countryCode,
    async () => {
        await fetchResults()
    },
    {
        debounce: 400,
    },
)

async function fetchResults(keyword) {
    try {
        isLoading.value = true
        const countryCode = props.countryCode

        if (!countryCode) return

        const { data } = await useGetCountryCities(countryCode, keyword)
        items.value = data
    } catch (error) {
        $root.$emit('flash.error', error)
    } finally {
        isLoading.value = false
    }
}
</script>
<template>
    <v-combobox
        v-model="selected"
        :items="items"
        :label="$t('labels.inputs.city')"
        :loading="isLoading"
        :search-input.sync="search"
        class="required-input"
        outline
        full-width
        hide-no-data
        v-bind="$attrs"
        @input="(input) => $emit('input', input)"
        @blur="(e) => $emit('input', search)"
    >
        <template #no-data>
            <v-list-tile>
                <v-list-tile-content>
                    <v-list-tile-title>
                        No results matching "<strong>{{ search }}</strong
                        >". Press <kbd>enter</kbd> to create a new one
                    </v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>
        </template>
    </v-combobox>
</template>
