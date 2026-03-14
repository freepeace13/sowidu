<template>
    <v-dialog
        v-model="isShow"
        width="400"
        persistent
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
                            ? $t('app_settings.labels.form.create-catalog-unit')
                            : $t('app_settings.labels.form.update-catalog-unit')
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
                            xs12
                            class="tw-flex tw-flex-col tw-gap-y-3"
                        >
                            <v-text-field
                                v-model="form.name"
                                color="primary"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.name"
                                :hide-details="!form.errors.name"
                                :label="
                                    $t('app_settings.labels.form.unit-name')
                                "
                                outline
                                required
                                class="required-input"
                            />
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-divider />
            <v-card-actions class="px-4 py-4">
                <v-spacer />
                <v-btn
                    :disabled="form.processing"
                    outline
                    depressed
                    @click="close"
                >
                    {{ $t('buttons.cancel') }}
                </v-btn>
                <SubmitButton
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="submit"
                >
                    {{ $t(isCreating ? 'buttons.create' : 'buttons.update') }}
                    <template #loader>
                        <span> {{ loadingText }}... </span>
                    </template>
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import SubmitButton from '@/Components/Forms/SubmitButton.vue'

export default {
    components: { SubmitButton },
    data: (vm) => ({
        form: vm.$inertia.form({
            name: null,
        }),
        isShow: false,
        unit: null,
    }),
    computed: {
        isCreating() {
            return !this.unit
        },
    },
    methods: {
        show(unit = null) {
            this.form.reset()
            if (unit) {
                this.unit = unit
                this.form.name = unit.name
            }
            this.isShow = true
        },
        close() {
            this.isShow = false
            this.reset()
        },
        reset() {
            this.item = null
            this.form.reset()
            this.form.clearErrors()
        },
        submit() {
            const route = this.isCreating
                ? this.$route('app.settings.catalogs.units.store')
                : this.$route('app.settings.catalogs.units.update', {
                      unit: this.unit,
                  })
            const method = this.isCreating ? 'post' : 'patch'
            this.form[method](route, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => this.close(),
                onError: (errors) =>
                    this.$root.$emit('flash.validation', errors),
            })
        },
    },
}
</script>
