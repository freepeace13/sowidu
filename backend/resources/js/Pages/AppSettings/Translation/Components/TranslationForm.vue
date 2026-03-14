<script setup>
import SubmitButton from '@/Components/Forms/SubmitButton.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { router, useForm } from '@inertiajs/vue2'
import { ref } from 'vue'

defineExpose({ show })

const { $route } = useGlobalVariables()

const form = useForm({
    text: null,
    key: null,
    group: null,
})

const isShow = ref(false)
const translation = ref(null)
const isLoading = ref(false)

function show(trans) {
    isLoading.value = true
    isShow.value = true

    form.key = trans.key

    router.reload({
        data: { trans: trans.key },
        only: ['trans'],
        preserveScroll: true,
        preserveState: true,
        onSuccess: ({ props: { trans } }) => {
            translation.value = trans

            form.text = trans.text?.de
        },
        onFinish: () => {
            isLoading.value = false
        },
    })
}

function close() {
    reset()
    isShow.value = false
}

function reset() {
    translation.value = null
    form.reset()
}

function submit() {
    form.post($route('app.settings.translation-manager.store'), {
        only: ['translationList', 'pagination'],
        onSuccess: () => close(),
    })
}
</script>
<template>
    <v-dialog
        v-model="isShow"
        width="600"
        persistent
        lazy
    >
        <v-card :loading="isLoading">
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{
                        $t(
                            'app_settings.translation-manager.labels.update-translation',
                        )
                    }}
                </v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="close"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-divider />
            <v-card-text>
                <v-container
                    grid-list-lg
                    fluid
                    pa-2
                >
                    <v-layout
                        row
                        wrap
                    >
                        <v-flex>
                            <div class="tw-text-gray-400">Key</div>
                            <div class="tw-font-semibold tw-text-lg mb-3">
                                {{ translation?.group }}.{{ translation?.key }}
                            </div>
                            <v-textarea
                                :value="translation?.text?.en"
                                outline
                                :loading="form.processing || isLoading"
                                readonly
                                hide-details
                                :label="
                                    $t(
                                        'app_settings.translation-manager.labels.en-value',
                                    )
                                "
                                no-resize
                                success
                            />
                        </v-flex>
                        <v-flex
                            xs12
                            class="tw-flex tw-flex-col tw-gap-y-3"
                        >
                            <v-textarea
                                v-model="form.text"
                                outline
                                :loading="form.processing || isLoading"
                                :disabled="form.processing || isLoading"
                                :error-messages="form.errors.value"
                                :hide-details="!form.errors.value"
                                :label="
                                    $t(
                                        'app_settings.translation-manager.labels.de-value',
                                    )
                                "
                            />
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>

            <v-divider />

            <v-card-actions class="px-4 py-4">
                <v-spacer />
                <v-btn
                    :disabled="form.processing || isLoading"
                    outline
                    depressed
                    @click="close"
                >
                    {{ $t('buttons.cancel') }}
                </v-btn>

                <SubmitButton
                    :loading="form.processing || isLoading"
                    :disabled="form.processing || isLoading"
                    @click="submit"
                >
                    {{ $t('buttons.update') }}
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
