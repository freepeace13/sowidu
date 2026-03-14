<script setup>
import { authCan } from '@/Composables/useAuth'
import { useDateFormat } from '@/Composables/useDayJs'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { router, useForm } from '@inertiajs/vue2'
import { useTimeoutPoll } from '@vueuse/core'
import { computed, onMounted, ref, toRef, watch } from 'vue'
import OfferItems from '../../Components/OfferItems.vue'
import PreviewOfferPdfModal from '../../Components/PreviewOfferPdfModal.vue'

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
    permissions: {
        required: true,
        type: Object,
    },
    offerUrl: {
        required: false,
        type: String,
        default: null,
    },
})

const { $t, $confirm, $route } = useGlobalVariables()

const offerItemsFormRef = ref(null)
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
const authCanModifyStatus = authCan('can modify offer status')

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
        only: ['offerUrl'],
        preserveState: true,
        preserveScroll: true,
    })
}

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

onMounted(() => {
    if (isAccepted.value) {
        resumePolling()
    }
})

function confirmAction(action) {
    const offer = props.offer

    const messages = {
        accept: $t('offer.messages.statuses.accepting'),
        reject: $t('offer.messages.statuses.rejecting'),
    }

    const buttonText = {
        accept: $t('offer.buttons.accept'),
        reject: $t('offer.buttons.reject'),
    }

    const routeName = {
        accept: 'my-offers.accept',
        reject: 'my-offers.reject',
    }

    const options = {
        accept: { submitColor: 'success' },
        reject: { submitColor: 'warning' },
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
                    only: ['permissions', 'offer'],
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

function downloadPdf() {
    const identifier = offer.value?.uuid ?? offer.value.id
    window.location.href = $route('offers.pdf.download', {
        offer: identifier,
    })
}
</script>
<template>
    <div>
        <v-toolbar
            color="white"
            flat
        >
            <v-toolbar-title class="title tw-flex tw-items-center">
                <div class="md:tw-text-xl tw-text-lg">
                    <v-btn
                        icon
                        @click="$inertia.get($route('my-offers.index'))"
                    >
                        <v-icon>arrow_back</v-icon>
                    </v-btn>
                    {{ $t('buttons.go-back') }}
                </div>
            </v-toolbar-title>

            <v-spacer />

            <PreviewOfferPdfModal
                :offer="offer"
                :is-editable="authCanEdit"
                @click:download="downloadPdf"
            />

            <!-- Accept -->
            <v-btn
                v-if="
                    authCanModifyStatus &&
                    statusInfo.is_sent &&
                    authCan('can accept offer')
                "
                color="success"
                depressed
                @click="confirmAction('accept')"
            >
                <v-icon left>recommend</v-icon>
                {{ $t('buttons.accept') }}
            </v-btn>

            <!-- Reject -->
            <v-btn
                v-if="
                    authCanModifyStatus &&
                    statusInfo.is_sent &&
                    authCan('can reject offer')
                "
                color="warning"
                depressed
                @click="confirmAction('reject')"
            >
                <v-icon left>back_hand</v-icon>
                {{ $t('offer.buttons.reject') }}
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
                                                class="tw-text-lg tw-text-black"
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
                                            v-if="authCanEdit"
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
                                                <tr
                                                    class="tw-text-sm tw-text-center"
                                                >
                                                    <td
                                                        colspan="4"
                                                        class="tw-text-right"
                                                    >
                                                        <strong>
                                                            {{
                                                                $t(
                                                                    'labels.amounts.subtotal',
                                                                )
                                                            }}
                                                        </strong>
                                                    </td>
                                                    <td
                                                        class="tw-whitespace-nowrap tw-font-semibold"
                                                    >
                                                        {{
                                                            offer?.subtotal_formatted
                                                        }}
                                                    </td>
                                                </tr>
                                                <tr
                                                    class="tw-text-sm tw-text-center"
                                                >
                                                    <td
                                                        colspan="4"
                                                        class="tw-text-right"
                                                    >
                                                        <strong>
                                                            {{
                                                                $t(
                                                                    'labels.amounts.grand_total',
                                                                )
                                                            }}
                                                        </strong>
                                                    </td>
                                                    <td
                                                        class="tw-whitespace-nowrap tw-font-semibold"
                                                    >
                                                        {{
                                                            offer?.grand_total_formatted
                                                        }}
                                                    </td>
                                                </tr>
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
                                        <!-- <div class="tw-text-right">
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
                                        </div> -->
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
