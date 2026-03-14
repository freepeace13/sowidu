<script>
import {
    useConvertDateTime,
    useDateFormat,
    useGetUserTimezone,
} from '@/Composables/useDayJs'
import SubmitButton from '@components/Forms/SubmitButton.vue'

export default {
    components: {
        SubmitButton,
    },

    props: {
        employees: {
            type: Array,
            required: true,
        },
    },

    data: (vm) => ({
        form: vm.$inertia.form({
            date_start: null,
            time_start: null,
            date_end: null,
            time_end: null,
            notes: null,
            event: null,
            employee: null,
            timezone: null,
            payment_form: null,
            document_number: null,
            document_date: null,
        }),
        timeStartReadable: null,
        timeEndReadable: null,
        isShow: false,
        workLog: null,
        dateMenu: false,
    }),

    computed: {
        workLogEventsOptions() {
            return this.$page.props.workLogEvents
        },

        isCreating() {
            return !this.workLog
        },

        canAddManualTimeLogToOthers() {
            return this.$page.props.user.can[
                'can add manual work log entry for others'
            ]
        },
    },

    methods: {
        show(workLog = null) {
            this.form.reset()

            if (workLog) {
                this.workLog = workLog
                this.form.date_start = useDateFormat(
                    workLog.started_at,
                    'YYYY-MM-DD',
                )

                this.form.date_end = useDateFormat(
                    workLog.ended_at,
                    'YYYY-MM-DD',
                )
                this.form.time_start = useDateFormat(
                    workLog.started_at,
                    'HH:mm',
                )
                this.form.time_end = useDateFormat(workLog.ended_at, 'HH:mm')

                this.timeStartReadable = useDateFormat(
                    workLog.started_at,
                    'hh:mm A',
                )
                this.timeEndReadable = useDateFormat(
                    workLog.ended_at,
                    'hh:mm A',
                )

                this.form.employee = workLog.causer.id
                this.form.event = workLog.event
                this.form.notes = workLog.reports[0]?.note ?? null
                this.form.payment_form = workLog.payment_form?.value ?? null
            } else {
                this.form.employee = this.employees.find(
                    (employee) => !!employee?.alias_name,
                )?.id
            }

            this.isShow = true
        },

        close() {
            this.isShow = false
            this.reset()
        },

        reset() {
            this.timeStartReadable = null
            this.timeEndReadable = null
            this.form.reset()
            this.form.clearErrors()
            this.workLog = null
        },

        submit() {
            const method = this.isCreating ? 'post' : 'patch'
            const route = this.isCreating
                ? this.$route('work_logs.manual_entries.store')
                : this.$route('work_logs.manual_entries.update', {
                      workLog: this.workLog,
                  })

            const paymentFormValue =
                typeof this.form.payment_form === 'object'
                    ? this.form.payment_form.value
                    : this.form.payment_form

            this.form.transform((data) => ({
                ...data,
                started_at: data.date_start + ' ' + data.time_start,
                ended_at: data.date_end + ' ' + data.time_end,
                timezone: useGetUserTimezone(),
                payment_form: paymentFormValue,
                document_number:
                    typeof data.payment_form === 'object'
                        ? data.payment_form.document_number
                        : data.document_number,
                document_date:
                    typeof data.payment_form === 'object'
                        ? data.payment_form.document_date
                        : data.document_date,
            }))

            this.form[method](route, {
                preserveState: true,
                preserveScroll: true,
                only: ['errors', 'flash'],
                onSuccess: () => {
                    this.$emit('refresh')
                    this.close()
                },
                onError: (errors) =>
                    this.$root.$emit('flash.validation', errors),
            })
        },

        timeStartChanged(val) {
            this.timeStartReadable = useConvertDateTime(val, 'HH:mm', 'LT')
        },

        timeEndChanged(val) {
            this.timeEndReadable = useConvertDateTime(val, 'HH:mm', 'LT')
        },

        dateStartChanged(val) {
            this.form.date_end = val
        },

        removeEmployee() {
            this.form.employee = null
        },
    },
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
                        isCreating
                            ? $t('work_log.labels.create-manual-entry')
                            : $t('work_log.labels.update-manual-entry')
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
                                :disabled="!canAddManualTimeLogToOthers"
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
                                    @input="dateStartChanged"
                                />
                            </v-menu>
                        </v-flex>
                        <v-flex xs6>
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
                                    scrollable
                                    @change="timeStartChanged"
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
                                :close-on-content-click="false"
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
                                    @change="timeEndChanged"
                                />
                            </v-menu>
                        </v-flex>
                        <v-select
                            v-model="form.payment_form"
                            :items="$page.props.paymentForms"
                            item-value="value"
                            item-text="text"
                            :label="$t('work_log.labels.form.payment_form')"
                            :loading="form.processing"
                            :error-messages="form.errors.payment_form"
                            :hide-details="!form.errors.payment_form"
                            outline
                        >
                            <template #item="{ item }">
                                <v-list-tile-content>
                                    <v-chip
                                        :color="item.color"
                                        text-color="white"
                                        label
                                        small
                                    >
                                        {{ item.text }}
                                    </v-chip>
                                </v-list-tile-content>
                            </template>

                            <template #selection="{ item }">
                                <v-chip
                                    :color="item.color"
                                    text-color="white"
                                    label
                                    small
                                >
                                    {{ item.text }}
                                </v-chip>
                            </template>
                        </v-select>
                        <v-flex xs12>
                            <v-text-field
                                v-model="form.document_number"
                                :error-messages="form.errors.document_number"
                                :hide-details="!form.errors.document_number"
                                label="Document Number"
                                outlined
                            />
                        </v-flex>

                        <v-flex xs12>
                            <v-menu
                                v-model="dateMenu"
                                :close-on-content-click="true"
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                            >
                                <template #activator="{ on }">
                                    <v-text-field
                                        v-model="form.document_date"
                                        :error-messages="
                                            form.errors.document_date
                                        "
                                        :hide-details="
                                            !form.errors.document_date
                                        "
                                        label="Document Date"
                                        outlined
                                        readonly
                                        v-on="on"
                                    />
                                </template>
                                <v-date-picker
                                    v-model="form.document_date"
                                    @input="dateMenu = false"
                                />
                            </v-menu>
                        </v-flex>

                        <v-flex xs12>
                            <v-select
                                v-model="form.event"
                                class="required-input"
                                required
                                :disabled="form.processing"
                                :items="workLogEventsOptions"
                                :loading="form.processing"
                                :error-messages="form.errors.event"
                                :hide-details="!form.errors.event"
                                :label="$t('work_log.labels.form.event')"
                                outline
                            />
                        </v-flex>
                        <v-flex xs12>
                            <v-textarea
                                v-model="form.notes"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :label="$t('work_log.labels.form.notes')"
                                :error-messages="form.errors.notes"
                                :hide-details="!form.errors.notes"
                                outline
                            />
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
