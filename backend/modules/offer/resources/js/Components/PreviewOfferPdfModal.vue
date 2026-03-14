<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { useClipboard } from '@vueuse/core'
import { computed, ref, watch } from 'vue'
import OfferSubjectForm from './OfferSubjectForm.vue'

defineExpose({
    refresh,
})

const props = defineProps({
    offer: {
        type: Object,
        required: true,
    },
    isEditable: {
        type: Boolean,
        required: true,
    },
})

defineEmits([
    'click:download',
    'click:edit-subject-description',
    'click:edit-offer-details',
])

const { $route, $root, $t } = useGlobalVariables()

const dialog = ref(false)
const offerSubjectFormRef = ref(null)
const { copy, copied } = useClipboard({ legacy: true })

const iframeLoaded = ref(false)
const refreshKey = ref(0)

const iframeSrc = computed(() => {
    const offer = props.offer?.uuid ?? props.offer.id
    return `${$route('offers.pdf.stream', {
        offer,
    })}?refresh=${refreshKey.value}`
})

watch(copied, (new_value) => {
    if (new_value) {
        $root.$emit('flash.success', $t('offer.messages.copied-to-clipboard'))
    }
})

watch(dialog, (new_value) => {
    if (new_value) {
        iframeLoaded.value = false
        refreshKey.value++
    }
})

function refresh() {
    iframeLoaded.value = false
    refreshKey.value++
}
</script>
<template>
    <v-dialog
        v-model="dialog"
        fullscreen
        hide-overlay
        transition="dialog-bottom-transition"
    >
        <OfferSubjectForm
            ref="offerSubjectFormRef"
            @refresh="refresh"
        />
        <template #activator="{ on }">
            <v-btn
                color="grey-lighter"
                class=""
                depressed
                v-on="on"
            >
                <v-icon left>visibility</v-icon>
                {{ $t('offer.buttons.preview') }}
            </v-btn>
        </template>
        <v-card>
            <v-toolbar
                dark
                color="primary"
            >
                <v-btn
                    icon
                    dark
                    @click="dialog = false"
                >
                    <v-icon>close</v-icon>
                </v-btn>
                <v-toolbar-title>
                    {{ $t('offer.buttons.preview') }}
                </v-toolbar-title>
                <v-spacer />
                <v-toolbar-items>
                    <v-menu offset-y>
                        <template #activator="{ on }">
                            <v-btn
                                depressed
                                color="info"
                                :disabled="!iframeLoaded || !isEditable"
                                v-on="on"
                            >
                                <v-icon left>edit</v-icon>
                                {{ $t('hints.edit') }}
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-tile
                                @click="
                                    $emit(
                                        'click:edit-subject-description',
                                        offer,
                                    )
                                "
                            >
                                <v-list-tile-title>
                                    {{
                                        $t(
                                            'offer.buttons.edit-subject-description',
                                        )
                                    }}
                                </v-list-tile-title>
                            </v-list-tile>
                            <v-list-tile
                                @click="
                                    $emit('click:edit-offer-details', offer)
                                "
                            >
                                <v-list-tile-title>
                                    {{ $t('offer.buttons.edit-offer-details') }}
                                </v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                    </v-menu>

                    <v-btn
                        color="primary"
                        depressed
                        :disabled="!iframeLoaded"
                        @click="
                            copy(
                                $route('offers.pdf.stream', {
                                    offer: offer.uuid,
                                }),
                            )
                        "
                    >
                        <v-icon left>share</v-icon>
                        {{ $t('buttons.share') }}
                    </v-btn>

                    <v-btn
                        color="primary"
                        depressed
                        :disabled="!iframeLoaded"
                        @click="$emit('click:download')"
                    >
                        <v-icon left>file_download</v-icon>
                        {{ $t('buttons.download-pdf') }}
                    </v-btn>
                </v-toolbar-items>
            </v-toolbar>

            <v-divider />

            <v-card-text>
                <v-progress-circular
                    v-if="dialog && !iframeLoaded"
                    indeterminate
                    color="primary"
                    class="mx-auto d-block my-8"
                    size="50"
                />
                <iframe
                    v-if="dialog"
                    :src="iframeSrc"
                    style="width: 100%; height: 93vh; border: none"
                    @load="iframeLoaded = true"
                />
            </v-card-text>
        </v-card>
    </v-dialog>
</template>
