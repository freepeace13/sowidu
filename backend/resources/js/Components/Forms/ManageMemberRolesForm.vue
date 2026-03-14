<template>
    <v-dialog
        v-model="show"
        max-width="650"
    >
        <v-card>
            <v-card-title class="title">
                {{ $t('buttons.manage-roles') }}
            </v-card-title>

            <v-card-text>
                <v-list class="pt-0">
                    <v-list-tile
                        v-show="isLoading || !employee.id"
                        class="tw-animate-pulse"
                        pl-0
                    >
                        <v-list-tile-avatar>
                            <SkeletonAvatar :size="48" />
                        </v-list-tile-avatar>

                        <v-list-tile-content>
                            <v-list-tile-title dense>
                                <SkeletonBar class="tw-w-3/12" />
                            </v-list-tile-title>
                            <v-list-tile-sub-title class="caption">
                                <SkeletonBar class="tw-w-64" />
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>

                    <v-list-tile
                        v-if="!isLoading && employee.id"
                        pl-1
                    >
                        <v-list-tile-avatar>
                            <v-avatar>
                                <v-img
                                    :src="employee.photo"
                                    :lazy-src="employee.photo"
                                />
                            </v-avatar>
                        </v-list-tile-avatar>
                        <v-list-tile-content>
                            <v-list-tile-title>
                                {{ employee.name }}
                            </v-list-tile-title>
                            <v-list-tile-sub-title class="caption">
                                {{ employee.user.email }}
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>

                <v-autocomplete
                    v-model="form.roles"
                    :items="roles"
                    :search-input.sync="newRole"
                    outline
                    hide-details
                    single-line
                    multiple
                    chips
                    deletable-chips
                    :loading="isLoading || form.processing"
                >
                    <template #no-data>
                        <v-list-tile @click="createNewRole">
                            <v-list-tile-avatar>
                                <v-icon>add</v-icon>
                            </v-list-tile-avatar>
                            <v-list-tile-content>
                                {{ newRole }}
                            </v-list-tile-content>
                        </v-list-tile>
                    </template>
                </v-autocomplete>
            </v-card-text>

            <v-card-actions>
                <v-spacer />

                <v-btn
                    :disabled="form.processing"
                    @click="close"
                >
                    {{ $t('buttons.cancel') }}
                </v-btn>

                <v-btn
                    color="primary"
                    :disabled="form.processing"
                    :loading="form.processing"
                    @click="save"
                >
                    {{ $t('buttons.save-changes') }}
                    <template #loader>
                        <span> {{ $t('buttons.saving') }} </span>
                    </template>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import SkeletonAvatar from '@/Components/Skeleton/SkeletonAvatar.vue'
import SkeletonBar from '@/Components/Skeleton/SkeletonBar.vue'

export default {
    components: { SkeletonBar, SkeletonAvatar },
    props: {
        employee: {
            type: Object,
            default: () => ({}),
        },
        roles: {
            required: true,
            type: Array,
        },
    },
    data: (vm) => ({
        show: false,
        isLoading: false,

        newRole: null,

        form: vm.$inertia.form({
            id: null,
            roles: [],
        }),
    }),

    methods: {
        async start(employee) {
            this.$inertia.reload({
                data: { employee },
                only: ['employee'],
                onBefore: () => {
                    this.isLoading = true
                    this.show = true
                },
                onFinish: () => {
                    const { id, roles } = this.employee
                    this.form.id = id
                    this.form.roles = roles

                    this.$nextTick(() => {
                        this.isLoading = false
                    })
                },
            })
        },

        createNewRole() {
            this.$inertia.post(
                this.$route('account.organizations.roles.store'),
                { name: this.newRole },
                {
                    onSuccess: () => {
                        this.form.roles.push(this.newRole)
                        this.newRole = null
                    },
                },
            )
        },

        close() {
            this.form.id = null
            this.form.roles = []
            this.show = false
            this.isLoading = true
        },

        save() {
            this.form.put(
                this.$route('account.employees.update', {
                    employee: this.form.id,
                }),
                {
                    onSuccess: () => {
                        this.$root.$emit(
                            'flash.success',
                            this.$t('account.messages.employee-role.updated'),
                        )
                        this.close()
                    },
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                },
            )
        },
    },
}
</script>
