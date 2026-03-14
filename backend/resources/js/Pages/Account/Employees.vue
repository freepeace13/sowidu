<template>
    <div>
        <div class="mb-5">
            <v-layout
                align-center
                justify-space-between
                :row="$vuetify.breakpoint.smAndUp"
                :column="$vuetify.breakpoint.xs"
            >
                <v-flex class="headline shrink font-weight-bold">
                    {{ $t('labels.employees') }}
                </v-flex>
                <v-flex
                    class="shrink sm:tw-flex sm:tw-justify-end"
                    :xs12="$vuetify.breakpoint.smAndDown"
                >
                    <v-btn
                        :disabled="!user.can['update settings']"
                        :small="$vuetify.breakpoint.smAndDown"
                        @click="$refs.manageRoles.show()"
                    >
                        {{ $t('buttons.manage-roles') }}
                    </v-btn>
                    <v-btn
                        :disabled="
                            !user.can['update settings'] &&
                            !user.can['add member']
                        "
                        :small="$vuetify.breakpoint.smAndDown"
                        @click="$refs.invite.start()"
                    >
                        {{ $t('buttons.add-member') }}
                    </v-btn>
                </v-flex>
            </v-layout>

            <v-divider class="mb-3" />

            <v-list>
                <EmployeeList
                    v-for="employed in employees"
                    :key="employed.id"
                    :employee="employed"
                    @click:manage-roles="({ id }) => $refs.roles.start(id)"
                    @click:manage-rates="
                        (employee) => $refs.manageRates.show(employee)
                    "
                />
            </v-list>
        </div>

        <div class="mb-5">
            <v-layout
                align-center
                justify-space-between
            >
                <v-flex class="headline shrink font-weight-bold">
                    {{ $t('headings.pending-invitations') }}
                </v-flex>
            </v-layout>

            <v-divider class="mb-3" />

            <v-list>
                <v-list-tile
                    v-for="invitation in pendingInvitations"
                    :key="invitation.id"
                    class="org-list"
                >
                    <v-list-tile-avatar>
                        <v-avatar
                            color="primary"
                            size="35"
                        >
                            <v-icon class="white--text">person</v-icon>
                        </v-avatar>
                    </v-list-tile-avatar>

                    <v-list-tile-content>
                        <v-list-tile-title>
                            {{ invitation.email }} &middot;
                            <small>{{ invitation.sent_at }}</small>
                        </v-list-tile-title>
                    </v-list-tile-content>

                    <v-list-tile-action>
                        <v-btn
                            color="red"
                            flat
                            @click="cancelInvitation(invitation)"
                        >
                            {{ $t('buttons.cancel') }}
                        </v-btn>
                    </v-list-tile-action>
                </v-list-tile>
            </v-list>

            <EmptyInvitations
                :show="!pendingInvitations.length"
                :allows-send-invitation="user.can['update settings']"
                @sending-invitation="() => $refs.invite.start()"
            />
        </div>

        <div class="mb-5">
            <v-layout
                align-center
                justify-space-between
            >
                <v-flex class="headline shrink font-weight-bold">
                    {{ $t('headings.failed-invitations') }}
                </v-flex>
            </v-layout>

            <v-divider class="mb-3" />

            <v-list>
                <v-list-tile
                    v-for="invitation in failedInvitations"
                    :key="invitation.id"
                >
                    <v-list-tile-avatar>
                        <v-avatar
                            color="red lighten-3"
                            size="35"
                        >
                            <v-icon class="white--text">person</v-icon>
                        </v-avatar>
                    </v-list-tile-avatar>

                    <v-list-tile-content>
                        <v-list-tile-title>
                            {{ invitation.email }}
                        </v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>

            <v-alert
                :value="!failedInvitations.length"
                color="info"
                icon="info"
                outline
            >
                {{ $t('messages.no-failed-invitations') }}
            </v-alert>
        </div>

        <AddMemberForm
            ref="invite"
            :roles="roles"
        />
        <ManageMemberRolesForm
            ref="roles"
            :employee="employee"
            :roles="roles"
        />
        <ManageRolesForm
            ref="manageRoles"
            v-bind="$props"
        />
        <ManageRatesForm ref="manageRates" />
    </div>
</template>

<script>
import EmptyInvitations from '@/Components/EmptyInvitations.vue'
import AddMemberForm from './Partials/AddMemberForm.vue'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import ManageMemberRolesForm from '@components/Forms/ManageMemberRolesForm.vue'
import AccountPageLayout from './AccountPageLayout.vue'
import ManageRolesForm from './Partials/ManageRolesForm.vue'
import EmployeeList from './Partials/EmployeeList.vue'
import ManageRatesForm from './Partials/ManageRatesForm.vue'

export default {
    components: {
        AddMemberForm,
        ManageMemberRolesForm,
        ManageRolesForm,
        EmptyInvitations,
        EmployeeList,
        ManageRatesForm,
    },

    layout: [AuthLayout, AccountPageLayout],

    props: {
        user: {
            required: true,
            type: Object,
            default: () => ({}),
        },

        failedInvitations: {
            type: Array,
            default: () => [],
        },

        employees: {
            type: Array,
            default: () => [],
        },

        pendingInvitations: {
            required: false,
            type: Array,
            default: () => [],
        },

        employee: {
            required: false,
            type: Object,
            default: () => ({
                id: null,
            }),
        },

        roles: {
            required: false,
            type: Array,
            default: () => [],
        },

        currency: {
            required: false,
            type: Object,
            default: () => ({
                symbol: '--',
                name: null,
            }),
        },
    },

    methods: {
        cancelInvitation(invitation) {
            this.$inertia.post(
                this.$route('account.employees.invitations.cancel', {
                    token: invitation.id,
                }),
            )
        },
    },
}
</script>
