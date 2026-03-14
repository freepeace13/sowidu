<template>
    <div>
        <div class="mb-5">
            <v-layout
                sm:tw-flex-nowrap
                tw-flex-wrap
                align-center
                justify-space-between
            >
                <v-flex class="headline shrink font-weight-bold">
                    {{ $t('headings.organizations') }}
                </v-flex>

                <v-flex
                    class="shrink"
                    tw-justify-end
                    sm:tw-mt-0
                    -tw-mt-4
                    sm:tw-w-auto
                    tw-w-full
                    tw-flex
                >
                    <v-btn
                        :small="$vuetify.breakpoint.smAndDown"
                        @click="$refs.createOrganization.start()"
                    >
                        {{ $t('buttons.new-organization') }}
                    </v-btn>
                </v-flex>
            </v-layout>

            <v-divider class="mb-3" />

            <v-list
                class="py-0"
                two-line
            >
                <v-list-tile
                    v-for="company in companies"
                    :key="company.id"
                    class="org-list"
                >
                    <v-list-tile-avatar>
                        <v-avatar size="45">
                            <v-img :src="company.photo" />
                        </v-avatar>
                    </v-list-tile-avatar>
                    <v-list-tile-content>{{
                        company.name
                    }}</v-list-tile-content>
                    <v-list-tile-action>
                        <v-btn
                            depressed
                            @click="leave(company)"
                        >
                            {{ $t('buttons.leave') }}
                        </v-btn>
                    </v-list-tile-action>
                </v-list-tile>
            </v-list>
        </div>

        <v-layout
            align-center
            justify-space-between
        >
            <v-flex class="headline shrink font-weight-bold">
                {{ $t('headings.invitations') }}
            </v-flex>
        </v-layout>

        <v-divider class="mb-3" />

        <v-list two-line>
            <v-list-tile
                v-for="invitation in invitations"
                :key="invitation.id"
                class="org-list"
            >
                <v-list-tile-avatar>
                    <v-img :src="invitation.company.photo" />
                </v-list-tile-avatar>

                <v-list-tile-content>
                    <v-list-tile-title>
                        {{ invitation.company.name }} &middot;
                        <small>{{ invitation.sent_at }}</small>
                    </v-list-tile-title>
                    <v-list-tile-sub-title v-if="invitation.note">
                        "{{ invitation.note }}"
                    </v-list-tile-sub-title>
                </v-list-tile-content>

                <v-list-tile-action>
                    <div class="d-inline-flex">
                        <v-btn
                            color="green"
                            flat
                            @click="acceptInvitation(invitation)"
                        >
                            {{ $t('buttons.accept') }}
                        </v-btn>

                        <v-btn
                            color="red"
                            flat
                            @click="declineInvitation(invitation)"
                        >
                            {{ $t('buttons.decline') }}
                        </v-btn>
                    </div>
                </v-list-tile-action>
            </v-list-tile>
        </v-list>

        <v-alert
            :value="!invitations.length"
            color="info"
            icon="info"
            outline
        >
            {{ $t('messages.empty-invitations') }}
        </v-alert>

        <CreateTeamForm ref="createOrganization" />
    </div>
</template>

<script>
import CreateTeamForm from './Partials/CreateTeamForm.vue'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import AccountPageLayout from '@/Pages/Account/AccountPageLayout.vue'

export default {
    components: {
        CreateTeamForm,
    },

    layout: [AuthLayout, AccountPageLayout],

    props: {
        companies: {
            type: Array,
            default: () => [],
        },

        invitations: {
            required: true,
            type: Array,
            default: () => [],
        },
    },

    methods: {
        acceptInvitation(invitation) {
            this.$inertia.post(
                this.$route('account.employees.invitations.accept', {
                    token: invitation.id,
                }),
            )
        },

        declineInvitation(invitation) {
            this.$inertia.post(
                this.$route('account.employees.invitations.decline', {
                    token: invitation.id,
                }),
            )
        },

        leave(company) {
            this.$confirm.ask({
                title: 'Leave',
                question: 'Are you sure you want to leave this organization?',
                type: 'delete',
                confirm: () => {
                    this.$inertia.post(
                        this.$route('account.organizations.leave', { company }),
                        {},
                        {
                            preserveState: true,
                            preserveScroll: true,
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    'You managed to leave on that organization.',
                                )
                            },
                        },
                    )
                },
            })
        },
    },
}
</script>
