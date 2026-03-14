<script setup>
import { ref } from 'vue'
import SubmitButton from '@components/Forms/SubmitButton.vue'
import { useForm } from '@inertiajs/vue2'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { useDateIsBefore, useDateFormat } from '@/Composables/useDayJs'

const { $t, $route, $confirm } = useGlobalVariables()

const order = ref(null)
const isShow = ref(false)
const form = useForm({
    planned_start_date: null,
    planned_finish_date: null,
})
const editStartDate = ref(false)

defineExpose({
    show,
})

function show(orderData, editFields = []) {
    order.value = orderData
    editStartDate.value = false

    if (editFields.includes('planned_start_date')) {
        editStartDate.value = true
    }

    form.planned_start_date = useDateFormat(
        orderData.planned_start_date,
        'YYYY-MM-DD',
        null,
    )
    form.planned_finish_date = useDateFormat(
        orderData.planned_finish_date,
        'YYYY-MM-DD',
        null,
    )

    isShow.value = true
}

function close() {
    form.reset()
    isShow.value = false
    editStartDate.value = false
    order.value = null
}

function submit() {
    form.patch($route('orders.update', { order: order.value.id }), {
        preserveScroll: true,
        only: ['order'],
        onSuccess: () => {
            close()
        },
    })
}

function checkSelectedDate(date) {
    if (useDateIsBefore(date)) {
        $confirm.ask({
            title: $t('labels.attention'),
            question: $t('order.hints.past-date'),
            type: 'info',
            confirm: () => {},
            cancel: () => {
                if (editStartDate.value) {
                    form.planned_start_date = null
                } else {
                    form.planned_finish_date = null
                }
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
                    {{
                        editStartDate
                            ? $t('order.labels.edit-planned-start-date')
                            : $t('order.labels.edit-planned-finish-date')
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
                        <v-flex
                            v-if="editStartDate"
                            xs12
                        >
                            <v-menu
                                :close-on-content-click="false"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                            >
                                <template #activator="{ on }">
                                    <v-text-field
                                        :value="form.planned_start_date"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        :error-messages="
                                            form.errors.planned_start_date
                                        "
                                        :hide-details="
                                            !form.errors.planned_start_date
                                        "
                                        :label="
                                            $t(
                                                'order.labels.inputs.planned-start-date',
                                            )
                                        "
                                        required
                                        readonly
                                        color="primary"
                                        outline
                                        v-on="on"
                                    />
                                </template>
                                <v-date-picker
                                    v-model="form.planned_start_date"
                                    :loading="form.processing"
                                    :disabled="form.processing"
                                    scrollable
                                    reactive
                                    picker-date
                                    @input="(val) => checkSelectedDate(val)"
                                />
                            </v-menu>
                        </v-flex>
                        <v-flex
                            v-if="!editStartDate"
                            xs12
                        >
                            <v-menu
                                :close-on-content-click="false"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                            >
                                <template #activator="{ on }">
                                    <v-text-field
                                        :value="form.planned_finish_date"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        :error-messages="
                                            form.errors.planned_finish_date
                                        "
                                        :hide-details="
                                            !form.errors.planned_finish_date
                                        "
                                        :label="
                                            $t(
                                                'order.labels.inputs.planned-finish-date',
                                            )
                                        "
                                        required
                                        readonly
                                        color="primary"
                                        outline
                                        v-on="on"
                                    />
                                </template>
                                <v-date-picker
                                    v-model="form.planned_finish_date"
                                    :loading="form.processing"
                                    :disabled="form.processing"
                                    scrollable
                                    reactive
                                    picker-date
                                    @input="(val) => checkSelectedDate(val)"
                                />
                            </v-menu>
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
                    {{ $t('buttons.update') }}
                    <template #loader>
                        <span> {{ $t('buttons.updating') }}... </span>
                    </template>
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
