<template>
    <div>
        <div class="mb-5">
            <v-layout
                align-center
                justify-space-between
            >
                <v-flex class="headline shrink font-weight-bold tw-capitalize">
                    {{ $t('account.labels.category-settings') }}
                </v-flex>
            </v-layout>
            <v-divider class="mb-3" />
            <v-layout
                column
                wrap
            >
                <v-flex class="tw-flex tw-justify-between tw-items-center">
                    <div class="headline tw-capitalize">
                        {{ category.name }}
                    </div>
                    <v-spacer />
                    <v-btn
                        color="info"
                        small
                        depressed
                        outline
                        @click="$root.$emit('category-form.show', category)"
                    >
                        {{ $t('account.buttons.edit') }}
                    </v-btn>
                    <v-btn
                        v-if="!category.is_default"
                        color="error"
                        small
                        depressed
                        @click="destroy"
                    >
                        {{ $t('buttons.delete') }}
                    </v-btn>
                </v-flex>
                <v-divider />
                <v-flex>
                    <v-list
                        subheader
                        two-line
                        class="pb-0"
                    >
                        <v-subheader>
                            {{
                                $t(
                                    'account.categories.labels.roles-where-media-shared',
                                )
                            }}
                        </v-subheader>

                        <v-list-tile
                            v-for="(role, key) in roles"
                            :key="`role-${key}`"
                            :disabled="roleIsOnMediaSettings(role)"
                            h-auto
                            class="my-3"
                        >
                            <v-list-tile-content>
                                <v-list-tile-title class="tw-capitalize">
                                    {{ role }}
                                </v-list-tile-title>
                                <v-list-tile-sub-title
                                    v-show="roleIsOnMediaSettings(role)"
                                    class="caption info--text"
                                >
                                    {{
                                        $t(
                                            'account.categories.labels.role-is-enabled-on-media-settings',
                                        )
                                    }}
                                </v-list-tile-sub-title>
                            </v-list-tile-content>

                            <v-list-tile-action>
                                <v-switch
                                    v-model="form.roles"
                                    color="primary"
                                    :value="role"
                                    :disabled="
                                        roleIsOnMediaSettings(role) ||
                                        form.processing
                                    "
                                    :loading="form.processing"
                                    @change="toggle"
                                />
                            </v-list-tile-action>
                        </v-list-tile>
                    </v-list>
                </v-flex>
            </v-layout>
        </div>
    </div>
</template>

<script>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import AccountPageLayout from './AccountPageLayout.vue'

export default {
    layout: [AuthLayout, AccountPageLayout],
    props: {
        category: {
            required: true,
            type: Object,
        },
        roles: {
            required: true,
            type: Array,
        },
    },
    data: (vm) => ({
        form: vm.$inertia.form({
            roles: vm.category.settings.auto_share_to_roles,
        }),
    }),
    methods: {
        roleIsOnMediaSettings(role) {
            return this.category.media_settings_auto_shared_to_roles.includes(
                role,
            )
        },

        toggle() {
            this.form
                .transform((data) => ({
                    ...data,
                    name: this.category.name,
                }))
                .patch(
                    this.$route('account.categories.update', {
                        category: this.category,
                    }),
                    {
                        preserveState: true,
                        preserveScroll: true,
                        only: ['errors', 'category'],
                        onError: (errors) =>
                            this.$root.$emit('flash.validation', errors),
                        onFinish: () =>
                            this.$inertia.reload({ only: ['category'] }),
                    },
                )
        },

        destroy() {
            this.$confirm.ask({
                title: this.$t('labels.delete'),
                question: this.$t('account.messages.category.confirm_deleting'),
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('account.categories.destroy', {
                            category: this.category,
                        }),
                        {
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    this.$t(
                                        'account.messages.category.deleted',
                                    ),
                                )
                            },
                            onError: (errors) =>
                                this.$root.$emit('flash.validation', errors),
                        },
                    )
                },
            })
        },
    },
}
</script>
