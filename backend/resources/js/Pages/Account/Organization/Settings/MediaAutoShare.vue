<template>
    <div>
        <div class="mb-5">
            <v-layout
                align-center
                justify-space-between
            >
                <v-flex class="headline shrink font-weight-bold">
                    {{ $t('account.labels.media-settings') }}
                </v-flex>
            </v-layout>
            <v-divider class="mb-3" />
            <v-layout
                column
                wrap
            >
                <v-flex class="subheading"> {{ $t('labels.media') }} </v-flex>
                <v-divider />
                <v-flex>
                    <v-list
                        subheader
                        two-line
                        class="pb-0"
                    >
                        <v-subheader>
                            {{ $t('account.labels.settings-applied-to') }}
                            <b class="ml-1">
                                {{
                                    $t('account.labels.organization-founder')
                                }} </b
                            >.
                        </v-subheader>
                        <v-list-group
                            no-action
                            :value="true"
                        >
                            <template #activator>
                                <v-list-tile>
                                    <v-list-tile-content>
                                        <v-list-tile-title class="body-2">
                                            {{
                                                $t(
                                                    'account.labels.auto-share-to-selected',
                                                )
                                            }}
                                            <b class="tw-lowercase">{{
                                                $t('labels.inputs.role')
                                            }}</b>
                                        </v-list-tile-title>
                                        <v-list-tile-sub-title class="caption">
                                            {{
                                                $t(
                                                    'account.labels.media-will-be-auto-shared-to-roles',
                                                )
                                            }}
                                        </v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </v-list-tile>
                            </template>
                            <v-subheader class="tw-h-8">
                                {{ $t('account.labels.select-roles') }}:
                            </v-subheader>
                            <v-list-tile
                                v-for="(role, key) in roles"
                                :key="`role-${key}`"
                                :disabled="role == 'Founder'"
                                h-auto
                                class="my-3"
                            >
                                <v-list-tile-content>
                                    <v-list-tile-title class="tw-capitalize">
                                        {{ role }}
                                    </v-list-tile-title>
                                </v-list-tile-content>

                                <v-list-tile-action>
                                    <v-switch
                                        v-model="form.roles"
                                        color="primary"
                                        :value="role"
                                        :disabled="
                                            role == 'Founder' || form.processing
                                        "
                                        :loading="form.processing"
                                        @change="toggle"
                                    />
                                </v-list-tile-action>
                            </v-list-tile>
                        </v-list-group>
                    </v-list>
                </v-flex>
            </v-layout>
        </div>
    </div>
</template>

<script>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import AccountPageLayout from '../../AccountPageLayout.vue'

export default {
    layout: [AuthLayout, AccountPageLayout],
    props: {
        roles: {
            type: Array,
            default: () => [],
        },
        autoShareToRoles: {
            required: true,
            type: Array,
        },
    },
    data: (vm) => ({
        form: vm.$inertia.form({
            roles: vm.autoShareToRoles,
        }),
    }),
    methods: {
        toggle() {
            const medium = 'auto_share_to_roles'
            this.form.patch(
                this.$route('account.settings.media.update', {
                    medium,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['errors', 'autoShareToRoles'],
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                    onFinish: () => this.form.reset('key'),
                },
            )
        },
    },
}
</script>
