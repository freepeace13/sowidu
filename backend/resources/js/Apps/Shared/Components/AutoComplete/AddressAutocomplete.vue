<script setup>
import { useAutocompleteAddress } from '@/Composables/useAutocomplete'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { useDebounceFn } from '@vueuse/core'
import { ref, watch } from 'vue'

const props = defineProps({
    value: {
        type: [String, Object],
        default: null,
    },
})

const emit = defineEmits(['input', 'click:create-new'])

const { $t, $root } = useGlobalVariables()

const selected = ref(null)
const search = ref(null)
const suggestions = ref([])
const isLoading = ref(false)

const fetchSuggestions = useDebounceFn(async (keyword) => {
    try {
        if (!keyword) return
        isLoading.value = true

        const { data } = await useAutocompleteAddress(keyword, 5)
        suggestions.value = data
    } catch (error) {
        $root.$emit('flash.error', error)
    } finally {
        isLoading.value = false
    }
}, 400)

watch(
    () => props.value,
    (val) => {
        selected.value = val
        suggestions.value = []

        if (!val || !val?.full) return

        suggestions.value = [val]
    },
)

watch(search, async (keyword) => {
    if (isLoading.value) return

    await fetchSuggestions(keyword)
})

function isChanged(value) {
    emit('input', { full: value, id: null })
}
</script>
<template>
    <v-combobox
        v-model="selected"
        outline
        no-filter
        full-width
        hide-selected
        append-icon="none"
        :items="suggestions"
        :loading="isLoading"
        :search-input.sync="search"
        :hide-details="true"
        v-bind="$attrs"
        @input="(value) => emit('input', value)"
        @keyup.enter="isChanged(selected)"
    >
        <template #selection="data">
            <slot
                name="data"
                v-bind="data"
            >
                {{ data.item.full }}
            </slot>
        </template>
        <template #item="{ item }">
            <v-list-tile-content>
                <v-list-tile-title>
                    {{ item.full }}
                </v-list-tile-title>
            </v-list-tile-content>
        </template>
        <template #no-data>
            <slot
                name="no-data"
                v-bind="search"
            >
                <v-list-tile
                    v-show="search"
                    ripple
                    @click.stop.prevent="emit('click:create-new')"
                    @mousedown.prevent="emit('click:create-new')"
                >
                    <v-list-tile-content>
                        <v-list-tile-title>
                            <v-icon>add</v-icon>
                            {{ $t('buttons.create-new-one') }}
                        </v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </slot>
        </template>
    </v-combobox>
</template>
