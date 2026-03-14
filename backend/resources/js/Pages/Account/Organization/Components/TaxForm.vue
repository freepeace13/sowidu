<script setup>
import { isNull } from '@/Composables/useUtils'
import { useForm } from '@inertiajs/vue2'
import { computed, ref } from 'vue'
import SubmitButton from '@components/Forms/SubmitButton.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'

defineExpose({
    show,
})

const { $route } = useGlobalVariables()
const isShow = ref(false)
const tax = ref(null)
const form = useForm({
    name: null,
    rate: 0,
    is_default: false,
})

const isCreating = computed(() => isNull(tax.value))

function show(data = null) {
    if (data) {
        tax.value = data

        form.name = data.name
        form.rate = data.rate
    }

    isShow.value = true
}

function close() {
    form.reset()
    isShow.value = false
}

function submit() {
    if (isCreating.value) {
        form.post($route('account.settings.tax.store'), {
            preserveScroll: true,
            preserveState: true,
            only: ['taxes'],
            onSuccess: () => {
                close()
            },
        })
    } else {
        form.put($route('account.settings.tax.update', tax.value.id), {
            preserveScroll: true,
            preserveState: true,
            only: ['taxes'],
            onSuccess: () => {
                close()
            },
        })
    }
}
</script>
<template>
    <v-dialog
        v-model="isShow"
        persistent
        max-width="600px"
        lazy
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ $t('account.tax.labels.tax-create') }}
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
                        <v-flex
                            xs12
                            sm6
                        >
                            <v-text-field
                                v-model="form.name"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.name"
                                :hide-details="!form.errors.name"
                                :label="$t('account.tax.labels.tax-name')"
                                required
                                color="primary"
                                outline
                                class="required-input"
                            />
                        </v-flex>
                        <v-flex
                            xs12
                            sm6
                        >
                            <v-text-field
                                v-model="form.rate"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.rate"
                                :hide-details="!form.errors.rate"
                                :label="$t('account.tax.labels.tax-rate')"
                                type="number"
                                required
                                color="primary"
                                outline
                                class="required-input"
                            />
                        </v-flex>

                        <v-flex xs12>
                            <v-checkbox
                                v-model="form.is_default"
                                hide-details
                                :disabled="form.processing"
                                :loading="form.processing"
                                color="primary"
                                class="mt-0"
                            >
                                <template #label>
                                    <div>
                                        {{
                                            $t(
                                                'account.tax.labels.tax-is-default',
                                            )
                                        }}
                                    </div>
                                </template>
                            </v-checkbox>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-divider />
            <v-card-actions class="px-4">
                <v-btn
                    :disabled="form.processing"
                    outline
                    depressed
                    @click="close"
                >
                    {{ $t('buttons.cancel') }}
                </v-btn>
                <v-spacer />
                <SubmitButton
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="submit"
                >
                    {{
                        isCreating ? $t('buttons.create') : $t('buttons.update')
                    }}
                    <template #loader>
                        <span>
                            {{
                                isCreating
                                    ? $t('buttons.creating')
                                    : $t('buttons.updating')
                            }}
                            ...
                        </span>
                    </template>
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
