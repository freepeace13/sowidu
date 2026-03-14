<script setup>
import { usePage } from '@inertiajs/vue2'
import { ref, toRef } from 'vue'
import useGlobalVariables from '~Shared/Composables/useGlobalVariables'

const props = defineProps({
    tile: {
        type: Boolean,
        default: false,
    },
    color: {
        type: String,
        default: 'grey darken-2',
    },
})

const { $route } = useGlobalVariables()
const isTile = toRef(props, 'tile')
const showTooltip = ref(false)
const invoice = usePage().props.invoice

const copyLink = async () => {
    const link = $route('invoicify.pdf.stream', { invoice: invoice.id })
    await navigator.clipboard.writeText(link)
    showTooltip.value = true
    setTimeout(() => {
        showTooltip.value = false
    }, 1500) // Tooltip visible for 1.5 seconds
}
</script>

<template>
    <v-tooltip
        v-model="showTooltip"
        bottom
        open-on-hover="false"
        open-on-focus="false"
    >
        <template #activator="{ attrs }">
            <v-list-tile
                v-if="isTile"
                v-bind="attrs"
                @click="copyLink"
            >
                <v-list-tile-avatar>
                    <v-icon :color="color">link</v-icon>
                </v-list-tile-avatar>
                <v-list-tile-content>
                    <v-list-tile-title>Copy Link</v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>

            <v-btn
                v-else
                :color="color"
                flat
                icon
                title="Copy Link"
                v-bind="attrs"
                @click="copyLink"
            >
                <v-icon>link</v-icon>
            </v-btn>
        </template>
        <span>Copied!</span>
    </v-tooltip>
</template>
