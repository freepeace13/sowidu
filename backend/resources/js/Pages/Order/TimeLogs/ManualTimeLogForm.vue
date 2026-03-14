<script setup>
import { authCan } from '@/Composables/useAuth'
import {
    useConvertDateTime,
    useDateDiffInSeconds,
    useDateNow,
    useGetDurationFromSeconds,
    useGetUserTimezone,
} from '@/Composables/useDayJs'
import { useGetPageProps } from '@/Composables/useGetPageProps'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import SubmitButton from '@components/Forms/SubmitButton.vue'
import { router, useForm } from '@inertiajs/vue2'
import { computed, ref } from 'vue'

defineExpose({ show })

const { $t, $route } = useGlobalVariables()

const form = useForm({
    date_start: null,
    time_start: null,
    date_end: null,
    time_end: null,
    timezone: null,
    employee: null,
})

const isShow = ref(null)
const timeStartMenuRef = ref(null)
const timeEndMenuRef = ref(null)

const timeStartReadable = computed(() => {
    if (!form.time_start) return null

    return useConvertDateTime(form.time_start, 'HH:mm', 'LT')
})

const timeEndReadable = computed(() => {
    if (!form.time_end) return null

    return useConvertDateTime(form.time_end, 'HH:mm', 'LT')
})

const canAddManualTimeLogToOthers = computed(() =>
    authCan('can_add_manual_time_log_to_others'),
)

const duration = computed(() => {
    if (
        !form.time_start ||
        !form.time_end ||
        !form.date_start ||
        !form.date_end
    ) {
        return null
    }

    const start = form.date_start + ' ' + form.time_start
    const end = form.date_end + ' ' + form.time_end

    const durationInSeconds = useDateDiffInSeconds(
        start,
        end,
        'YYYY-MM-DD HH:mm',
    )

    if (durationInSeconds < 0) {
        handleErrors()

        return null
    }

    form.clearErrors()

    return useGetDurationFromSeconds(durationInSeconds)
})

const employees = computed(() => useGetPageProps('employees', []))

function handleErrors() {
    if (
        useDateDiffInSeconds(form.date_start, form.date_end, 'YYYY-MM-DD') < 0
    ) {
        form.setError(
            'date_start',
            $t('validation.before', {
                attribute: 'start date',
                date: 'end date',
            }),
        )

        form.setError(
            'date_end',
            $t('validation.after', {
                attribute: 'end date',
                date: 'start date',
            }),
        )
    } else {
        form.setError(
            'time_start',
            $t('validation.before', {
                attribute: 'start time',
                date: 'end time',
            }),
        )

        form.setError(
            'time_end',
            $t('validation.after', {
                attribute: 'end time',
                date: 'start time',
            }),
        )
    }
}

function show() {
    form.processing = true

    form.date_start = useDateNow()
    form.date_end = useDateNow()

    router.reload({
        only: ['employees'],
        onSuccess: () => {
            form.employee = employees.value.find(
                (employee) => !!employee?.alias_name,
            )?.id
        },
        onFinish: () => {
            form.processing = false
            isShow.value = true
        },
    })
}

function close() {
    isShow.value = false
    reset()
}

function reset() {
    form.reset()
    form.clearErrors()
}

function submit() {
    const order = useGetPageProps('order')

    form.transform((data) => ({
        ...data,
        started_at: data.date_start + ' ' + data.time_start,
        ended_at: data.date_end + ' ' + data.time_end,
        timezone: useGetUserTimezone(),
    })).post(
        $route('orders.show.time_logs.store', {
            order,
        }),
        {
            only: ['timeLogs', 'totalTime'],
            onSuccess: () => {
                close()
            },
        },
    )
}

function removeEmployee() {
    form.employee = null
}
</script>
<template>
    <v-dialog
        :value="isShow"
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
                    {{ $t('order.work_log.create-manual-entry') }}
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
            <v-card-text class="mb-2">
                <v-container
                    grid-list-lg
                    fluid
                    pa-2
                >
                    <v-layout
                        row
                        wrap
                    >
                        <v-flex xs12>
                            <v-select
                                v-model="form.employee"
                                :items="employees"
                                :menu-props="{
                                    nudgeBottom: 38,
                                    closeOnContentClick: true,
                                }"
                                solo
                                chips
                                small-chips
                                deletable-chips
                                outline
                                flat
                                hide-selected
                                item-value="id"
                                :label="$t('work_log.labels.form.employee')"
                                :hide-details="!form.errors.employee"
                                :error-messages="form.errors.employee"
                                :disabled="
                                    !canAddManualTimeLogToOthers ||
                                    form.processing
                                "
                            >
                                <template #item="{ item }">
                                    <v-list-tile-content>
                                        <v-chip
                                            color="primary"
                                            text-color="white"
                                            label
                                            small
                                        >
                                            <v-avatar>
                                                <img
                                                    :src="item.photo"
                                                    :alt="item.name"
                                                />
                                            </v-avatar>
                                            <span>
                                                {{
                                                    item?.alias_name ??
                                                    item.name
                                                }}
                                            </span>
                                        </v-chip>
                                    </v-list-tile-content>
                                </template>
                                <template #selection="{ item }">
                                    <v-chip
                                        color="primary"
                                        text-color="white"
                                        label
                                        small
                                        close
                                        @input="removeEmployee"
                                    >
                                        <v-avatar>
                                            <img
                                                :src="item.photo"
                                                :alt="item.name"
                                            />
                                        </v-avatar>
                                        {{ item?.alias_name ?? item.name }}
                                    </v-chip>
                                </template>
                            </v-select>
                        </v-flex>
                        <v-flex xs6>
                            <v-menu
                                :close-on-content-click="true"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                            >
                                <template #activator="{ on }">
                                    <v-text-field
                                        :value="form.date_start"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        :error-messages="form.errors.date_start"
                                        :hide-details="!form.errors.date_start"
                                        :label="
                                            $t(
                                                'work_log.labels.form.start-date',
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
                                    v-model="form.date_start"
                                    :loading="form.processing"
                                    :disabled="form.processing"
                                    scrollable
                                    reactive
                                    picker-date
                                />
                            </v-menu>
                        </v-flex>
                        <v-flex xs6>
                            <v-menu
                                ref="timeStartMenuRef"
                                :close-on-content-click="false"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                            >
                                <template #activator="{ on }">
                                    <v-text-field
                                        :value="timeStartReadable"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        :error-messages="form.errors.time_start"
                                        :hide-details="!form.errors.time_start"
                                        :label="
                                            $t(
                                                'work_log.labels.form.start-time',
                                            )
                                        "
                                        required
                                        readonly
                                        color="primary"
                                        outline
                                        v-on="on"
                                    />
                                </template>
                                <v-time-picker
                                    v-model="form.time_start"
                                    :loading="form.processing"
                                    :disabled="form.processing"
                                    @click:minute="
                                        (time) => timeStartMenuRef.save(time)
                                    "
                                />
                            </v-menu>
                        </v-flex>
                        <v-flex xs6>
                            <v-menu
                                :close-on-content-click="true"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                            >
                                <template #activator="{ on }">
                                    <v-text-field
                                        :value="form.date_end"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        :error-messages="form.errors.date_end"
                                        :hide-details="!form.errors.date_end"
                                        :label="
                                            $t('work_log.labels.form.end-date')
                                        "
                                        required
                                        readonly
                                        color="primary"
                                        outline
                                        v-on="on"
                                    />
                                </template>
                                <v-date-picker
                                    v-model="form.date_end"
                                    :loading="form.processing"
                                    :disabled="form.processing"
                                    scrollable
                                    reactive
                                    picker-date
                                />
                            </v-menu>
                        </v-flex>
                        <v-flex xs6>
                            <v-menu
                                ref="timeEndMenuRef"
                                :close-on-content-click="false"
                                :close-on-click="true"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                            >
                                <template #activator="{ on }">
                                    <v-text-field
                                        :value="timeEndReadable"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        :error-messages="form.errors.time_end"
                                        :hide-details="!form.errors.time_end"
                                        :label="
                                            $t('work_log.labels.form.end-time')
                                        "
                                        required
                                        readonly
                                        color="primary"
                                        outline
                                        v-on="on"
                                    />
                                </template>
                                <v-time-picker
                                    v-model="form.time_end"
                                    :loading="form.processing"
                                    :disabled="form.processing"
                                    scrollable
                                    @click:minute="
                                        (time) => timeEndMenuRef.save(time)
                                    "
                                />
                            </v-menu>
                        </v-flex>
                        <v-flex
                            offset-xs6
                            xs6
                            class="tw-flex tw-flex-row tw-items-center tw-justify-between"
                        >
                            <div
                                :class="[
                                    'tw-font-semibold',
                                    {
                                        'error--text': !duration,
                                    },
                                ]"
                            >
                                {{ $t('work_log.labels.duration') }}:
                            </div>

                            <v-chip
                                label
                                :color="!duration ? 'error' : 'primary'"
                                text-color="white"
                            >
                                <v-icon left> schedule </v-icon>
                                <span class="tw-font-bold">
                                    {{ duration ?? '--' }}
                                </span>
                            </v-chip>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-divider />
            <v-card-actions class="px-4 py-3">
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
                    {{ $t('buttons.create') }}
                    <template #loader>
                        <span>
                            {{ $t('buttons.creating') }}
                            ...
                        </span>
                    </template>
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
