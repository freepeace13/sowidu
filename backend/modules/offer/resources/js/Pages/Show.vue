<script setup>
import SubmitButton from '@/Components/Forms/SubmitButton.vue'
import { authCan } from '@/Composables/useAuth'
import { useDateFormat } from '@/Composables/useDayJs'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { router, useForm } from '@inertiajs/vue2'
import EditOfferDetailsForm from '@Offer/Components/EditOfferDetailsForm.vue'
import OfferSubjectForm from '@Offer/Components/OfferSubjectForm.vue'
import OfferTotalRow from '@Offer/Components/OfferTotalRow.vue'
import { useTimeoutPoll } from '@vueuse/core'
import { computed, onMounted, ref, toRef, watch } from 'vue'
import OfferItems from '../Components/OfferItems.vue'
import OfferItemsForm from '../Components/OfferItemsForm.vue'
import PreviewOfferPdfModal from '../Components/PreviewOfferPdfModal.vue'
import OfferManualItemForm from '@Offer/Components/OfferManualItemForm.vue'

const props = defineProps({
    offer: {
        required: true,
        type: Object,
    },
    items: {
        required: false,
        type: Array,
        default: () => [],
    },
    offerUrl: {
        required: false,
        type: String,
        default: null,
    },
})

const { $t, $confirm, $route } = useGlobalVariables()

const offerItemsFormRef = ref(null)
const editOfferDetailsFormRef = ref(null)
const offerSubjectFormRef = ref(null)
const previewOfferPdfModalRef = ref(null)
const offerManualItemFormRef = ref(null)

const isFocusedOnDescription = ref(false)
const offer = toRef(props, 'offer')
const form = useForm({
    description: props.offer?.description,
})

const recipient = computed(() => offer.value.recipient)
const statusInfo = computed(() => offer.value.status_metadata)
const isDraft = computed(() => statusInfo.value.is_draft)
const isAccepted = computed(() => statusInfo.value.is_accepted)
const offerUrl = computed(() => props.offerUrl)

watch(
    () => isAccepted.value,
    (new_value) => {
        if (new_value) {
            reloadOfferUrl()
        }
    },
)

function reloadOfferUrl() {
    router.reload({
        only: ['offerUrl', 'permissions'],
        preserveState: true,
        preserveScroll: true,
    })
}

// useTimeoutPoll for cleaner polling
const {
    isActive: isPollingActive,
    pause: pausePolling,
    resume: resumePolling,
} = useTimeoutPoll(
    async () => {
        // Check if offerUrl is present and not null
        if (offerUrl.value && offerUrl.value !== null) {
            pausePolling()
            return
        }

        reloadOfferUrl()
    },
    2000, // 2 seconds interval
)

watch(
    () => isAccepted.value,
    (new_value) => {
        if (new_value) {
            resumePolling()
        } else {
            pausePolling()
        }
    },
)

onMounted(() => {
    if (isAccepted.value) {
        resumePolling()
    }
})

const descriptionWasChanged = computed(
    () => offer.value.description != form.description,
)
const alertInfo = computed(() => {
    return {
        pending: {
            color: 'info',
            message: $t('offer.messages.current_status.sent'),
        },
        accepted: {
            color: 'success',
            message: $t('offer.messages.current_status.accepted'),
        },
        rejected: {
            color: 'warning',
            message: $t('offer.messages.current_status.rejected'),
        },
        cancelled: {
            color: 'error',
            message: $t('offer.messages.current_status.cancelled'),
        },
    }[offer.value.status]
})

const authCanEdit = authCan('can edit offer')
const authCanDelete = authCan('can delete offer')
const authCanModifyStatus = authCan('can modify offer status')

function updateDescription() {
    form.transform((data) => ({
        ...data,
        recipient: {
            id: offer.value.recipientable_id,
            type: offer.value.recipientable_type,
        },
        type: offer.value.type.value,
        title: offer.value.title,
        offer_date: useDateFormat(offer.value.offer_date, 'YYYY-MM-DD'),
    })).patch(
        $route('offers.update', {
            offer: props.offer,
        }),
        {
            preserveScroll: true,
            preserveState: true,
            only: ['offer'],
        },
    )
}

function confirmAction(action) {
    const offer = props.offer

    const messages = {
        send: $t('offer.messages.statuses.sending'),
        accept: $t('offer.messages.statuses.accepting'),
        reject: $t('offer.messages.statuses.rejecting'),
        cancel: $t('offer.messages.statuses.cancelling'),
    }

    const buttonText = {
        send: $t('offer.buttons.send_offer'),
        accept: $t('offer.buttons.accept'),
        reject: $t('offer.buttons.reject'),
        cancel: $t('buttons.cancel'),
    }

    const routeName = {
        send: 'offers.status.send',
        accept: 'offers.status.accept',
        reject: 'offers.status.reject',
        cancel: 'offers.status.cancel',
    }

    const options = {
        send: { submitColor: 'info' },
        accept: { submitColor: 'success' },
        reject: { submitColor: 'warning' },
        cancel: { submitColor: 'error' },
    }

    $confirm({
        title: $t('labels.warning'),
        question: messages[action],
        type: 'info',
        buttonText: buttonText[action],
        confirm: () => {
            router.post(
                $route(routeName[action], {
                    offer,
                }),
                {},
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['offer', 'permissions'],
                    onSuccess: () => {
                        if (action == 'accept') {
                            resumePolling()
                        }
                    },
                },
            )
        },
        options: {
            ...options[action],
        },
    })
}

function confirmDeleting() {
    $confirm({
        title: $t('labels.delete'),
        question: $t('offer.messages.deleting'),
        type: 'delete',
        confirm: () => {
            router.delete(
                $route('offers.destroy', {
                    offer,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                },
            )
        },
    })
}

function downloadPdf() {
    const identifier = offer.value?.uuid ?? offer.value.id
    window.location.href = $route('offers.pdf.download', {
        offer: identifier,
    })
}
</script>
<template>
    <div>
        <EditOfferDetailsForm
            ref="editOfferDetailsFormRef"
            @refresh="previewOfferPdfModalRef.refresh()"
        />
        <OfferItemsForm
            ref="offerItemsFormRef"
            :offer="offer"
        />
        <OfferSubjectForm
            ref="offerSubjectFormRef"
            @refresh="previewOfferPdfModalRef.refresh()"
        />

        <OfferManualItemForm
            ref="offerManualItemFormRef"
            :offer="offer"
            @refresh="$inertia.reload({ only: ['items', 'offer'] })"
        />

        <v-toolbar
            color="white"
            flat
        >
            <v-toolbar-title class="title tw-flex tw-items-center">
                <div class="md:tw-text-xl tw-text-lg">
                    <v-btn
                        icon
                        @click="$inertia.get($route('offers.index'))"
                    >
                        <v-icon>arrow_back</v-icon>
                    </v-btn>
                    {{ $t('offer.labels.message') }}
                </div>
            </v-toolbar-title>

            <v-spacer />

            <PreviewOfferPdfModal
                ref="previewOfferPdfModalRef"
                :offer="offer"
                :is-editable="authCanEdit"
                @click:edit-offer-details="editOfferDetailsFormRef.show(offer)"
                @click:edit-subject-description="
                    offerSubjectFormRef.show(offer)
                "
                @click:download="downloadPdf"
            />

            <!-- Accept -->
            <v-btn
                v-if="authCanModifyStatus && statusInfo.is_sent"
                color="success"
                depressed
                @click="confirmAction('accept')"
            >
                <v-icon left>recommend</v-icon>
                {{ $t('buttons.accept') }}
            </v-btn>

            <!-- Reject -->
            <v-btn
                v-if="authCanModifyStatus && statusInfo.is_sent"
                color="warning"
                depressed
                @click="confirmAction('reject')"
            >
                <v-icon left>back_hand</v-icon>
                {{ $t('offer.buttons.reject') }}
            </v-btn>

            <!-- Send -->
            <v-btn
                v-if="isDraft && authCanModifyStatus"
                color="info"
                depressed
                @click="confirmAction('send')"
            >
                <v-icon left>email</v-icon>
                {{ $t('offer.buttons.send_offer') }}
            </v-btn>

            <!-- Cancel -->
            <v-btn
                v-if="authCanModifyStatus && statusInfo.is_sent"
                color="#F44336"
                depressed
                class="white--text"
                @click="confirmAction('cancel')"
            >
                <v-icon left>cancel</v-icon>
                {{ $t('buttons.cancel') }}
            </v-btn>

            <v-btn
                v-if="authCanEdit && isDraft"
                color="secondary"
                depressed
                @click="editOfferDetailsFormRef.show(offer)"
            >
                <v-icon left>edit</v-icon>
                {{ $t('buttons.edit') }}
            </v-btn>

            <v-btn
                v-if="authCanDelete && isDraft"
                color="error"
                @click="confirmDeleting"
            >
                <v-icon left>delete</v-icon>
                {{ $t('buttons.delete') }}
            </v-btn>
        </v-toolbar>
        <v-divider />
        <v-container fluid>
            <v-layout
                row
                wrap
                fill-height
            >
                <v-flex
                    xs12
                    class="!tw-overflow-auto !tw-grow elevation-10"
                >
                    <v-card
                        flat
                        class=""
                    >
                        <v-card-text>
                            <v-container
                                grid-list-md
                                fluid
                                pa-2
                            >
                                <v-layout
                                    row
                                    wrap
                                >
                                    <v-flex
                                        xs7
                                        class="tw-text-lg"
                                    >
                                        <div class="tw-font-semibold">
                                            {{ $t('offer.inputs.recipient') }}:
                                        </div>

                                        <div>{{ recipient?.name }}</div>
                                        <div>
                                            <v-img
                                                :src="recipient?.photo"
                                                contain
                                                class="grey lighten-2 mr-2"
                                                height="50"
                                                width="50"
                                            />
                                        </div>
                                    </v-flex>
                                    <v-flex
                                        xs5
                                        class="tw-flex tw-justify-end"
                                    >
                                        <div
                                            class="tw-grid tw-grid-cols-2 tw-gap-x-3"
                                        >
                                            <div
                                                class="theme--light v-label tw-text-right tw-pt-[2px]"
                                            >
                                                {{
                                                    $t(
                                                        'offer.labels.offer_number',
                                                    )
                                                }}:
                                            </div>
                                            <div
                                                class="tw-text-lg tw-text-black"
                                            >
                                                {{ offer?.internal_id }}
                                            </div>
                                            <div
                                                class="theme--light v-label tw-text-right tw-pt-[2px]"
                                            >
                                                {{
                                                    $t(
                                                        'offer.inputs.offer_date',
                                                    )
                                                }}:
                                            </div>
                                            <div
                                                class="tw-text-lg tw-text-black tw-bg-"
                                            >
                                                {{
                                                    useDateFormat(
                                                        offer?.offer_date,
                                                    )
                                                }}
                                            </div>
                                            <div
                                                class="theme--light v-label tw-text-right tw-mt-[5px]"
                                            >
                                                {{ $t('labels.status') }}:
                                            </div>
                                            <div
                                                class="tw-text-lg tw-text-black"
                                            >
                                                <VChip
                                                    small
                                                    label
                                                    :color="statusInfo?.color"
                                                    :class="[
                                                        'tw-capitalize',
                                                        'tw-font-semibold',
                                                        {
                                                            'white--text':
                                                                !isDraft,
                                                        },
                                                    ]"
                                                >
                                                    {{ statusInfo?.label }}
                                                </VChip>
                                            </div>
                                        </div>
                                    </v-flex>
                                    <v-flex
                                        xs8
                                        class="tw-flex tw-gap-x-2"
                                    >
                                        <div class="tw-font-semibold">
                                            {{
                                                $t(
                                                    'offer.inputs.construction_site',
                                                )
                                            }}:
                                        </div>
                                        <div>
                                            {{
                                                offer?.construction_site
                                                    ?.short_full_address
                                            }}
                                        </div>
                                    </v-flex>
                                    <v-flex
                                        xs4
                                        class="tw-flex tw-gap-x-2"
                                    >
                                        <div class="tw-font-semibold">
                                            {{
                                                $t(
                                                    'offer.inputs.execution_period',
                                                )
                                            }}:
                                        </div>
                                        <div>
                                            {{
                                                useDateFormat(
                                                    offer?.execution_period_start,
                                                )
                                            }}
                                            -
                                            {{
                                                useDateFormat(
                                                    offer?.execution_period_end,
                                                )
                                            }}
                                        </div>
                                    </v-flex>
                                    <v-flex xs12>
                                        <v-alert
                                            :value="!isDraft"
                                            :color="alertInfo?.color"
                                            icon="check_circle"
                                            outline
                                        >
                                            <div
                                                class="tw-flex tw-items-center tw-justify-between"
                                            >
                                                {{ alertInfo?.message }}

                                                <a
                                                    v-if="
                                                        statusInfo?.is_accepted
                                                    "
                                                    :href="offerUrl"
                                                    target="_blank"
                                                >
                                                    <v-btn
                                                        color="success"
                                                        :loading="
                                                            isPollingActive
                                                        "
                                                        :disabled="
                                                            isPollingActive
                                                        "
                                                    >
                                                        {{
                                                            $t(
                                                                'offer.buttons.view-order',
                                                            )
                                                        }}

                                                        <v-icon
                                                            right
                                                            small
                                                        >
                                                            open_in_new
                                                        </v-icon>
                                                    </v-btn>
                                                </a>
                                            </div>
                                        </v-alert>
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        class="tw-flex tw-items-center mt-2"
                                    >
                                        <div
                                            class="tw-text-lg tw-font-semibold"
                                        >
                                            {{ $t('offer.labels.items') }}:
                                        </div>
                                        <v-spacer />

                                        <v-btn
                                            v-if="authCanEdit && isDraft"
                                            color="secondary"
                                            @click="
                                                offerManualItemFormRef.show()
                                            "
                                        >
                                            {{
                                                $t(
                                                    'offer.buttons.attach-manual-item',
                                                )
                                            }}
                                        </v-btn>

                                        <v-btn
                                            v-if="authCanEdit && isDraft"
                                            color="info"
                                            @click="offerItemsFormRef.show()"
                                        >
                                            {{
                                                $t('offer.buttons.attach_item')
                                            }}
                                        </v-btn>
                                    </v-flex>

                                    <v-flex xs12>
                                        <v-alert
                                            v-if="!items.length"
                                            :value="!items.length"
                                            color="info"
                                            icon="info"
                                            outline
                                            class="pa-2"
                                        >
                                            <div
                                                class="!tw-flex !tw-items-center tw-justify-between"
                                            >
                                                <div>
                                                    {{
                                                        $t(
                                                            'offer.messages.items.empty',
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                        </v-alert>
                                        <OfferItems
                                            v-else
                                            :items="items"
                                            :offer="offer"
                                            :editable="authCanEdit"
                                            card-text-class="px-0"
                                            @click:add-item="
                                                (offer) =>
                                                    $emit(
                                                        'click:add-item',
                                                        offer,
                                                    )
                                            "
                                        >
                                            <template #totals>
                                                <OfferTotalRow
                                                    :label="
                                                        $t(
                                                            'labels.amounts.subtotal',
                                                        )
                                                    "
                                                    :value="
                                                        offer?.subtotal_formatted
                                                    "
                                                />

                                                <tr class="spacer-row">
                                                    <td colspan="5" />
                                                </tr>
                                                <OfferTotalRow
                                                    :label="
                                                        $t(
                                                            'labels.amounts.net_amount',
                                                        )
                                                    "
                                                    :value="
                                                        offer?.net_amount_formatted
                                                    "
                                                />
                                                <OfferTotalRow
                                                    v-for="tax in offer?.taxes"
                                                    :key="tax.name"
                                                    :label="`${tax.name} (${tax.rate}%)`"
                                                    :value="
                                                        tax.amount_formatted
                                                    "
                                                />
                                                <OfferTotalRow
                                                    :label="
                                                        $t(
                                                            'labels.amounts.grand_total',
                                                        )
                                                    "
                                                    :value="
                                                        offer?.grand_total_formatted
                                                    "
                                                />
                                            </template>
                                        </OfferItems>
                                    </v-flex>

                                    <v-flex
                                        xs12
                                        mt-4
                                    >
                                        <div
                                            class="tw-text-lg tw-font-semibold"
                                        >
                                            {{
                                                $t('offer.inputs.description')
                                            }}:
                                        </div>
                                    </v-flex>
                                    <v-flex xs12>
                                        <v-textarea
                                            v-model="form.description"
                                            :disabled="
                                                form.processing || !isDraft
                                            "
                                            :loading="form.processing"
                                            :error-messages="
                                                form.errors.description
                                            "
                                            :hide-details="
                                                !form.errors.description
                                            "
                                            outline
                                            auto-grow
                                            :class="[
                                                'description-textarea',
                                                {
                                                    'mb-5': !isFocusedOnDescription,
                                                    'mb-0': isFocusedOnDescription,
                                                },
                                            ]"
                                            @focusin="
                                                () => {
                                                    if (!authCanEdit) return

                                                    isFocusedOnDescription = true
                                                }
                                            "
                                            @focusout="
                                                () => {
                                                    if (!authCanEdit) return

                                                    isFocusedOnDescription = false
                                                }
                                            "
                                        />
                                        <div class="tw-text-right">
                                            <SubmitButton
                                                v-show="
                                                    isFocusedOnDescription ||
                                                    descriptionWasChanged
                                                "
                                                :is-processing="form.processing"
                                                class="mx-0"
                                                @click="updateDescription"
                                            >
                                                {{ $t('buttons.save-changes') }}
                                            </SubmitButton>
                                        </div>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-text>
                        <v-divider />
                    </v-card>
                </v-flex>
            </v-layout>
        </v-container>
    </div>
</template>
<style lang="scss" scoped>
.description-textarea {
    max-height: 250px;
    min-height: 150px;
    overflow-y: auto;

    ::v-deep .v-input__slot textarea {
        margin-top: 0px !important;
    }
}
</style>
