<template>
    <div>
        <div class="mb-5">
            <v-layout
                align-center
                justify-space-between
            >
                <v-flex class="headline shrink font-weight-bold">
                    {{
                        user.impersonating
                            ? $t('account.labels.organizations-profile')
                            : $t('account.labels.profile')
                    }}
                </v-flex>
            </v-layout>

            <v-divider class="mb-3" />

            <v-card
                color="transparent"
                flat
                :width="$vuetify.breakpoint.mdAndUp ? '70%' : '100%'"
            >
                <TeamInformationForm
                    v-cloak
                    v-if="user.impersonating"
                    v-bind="$props"
                />
                <UserInformationForm
                    v-cloak
                    v-else
                />
            </v-card>
        </div>
    </div>
</template>

<script>
import TeamInformationForm from './Partials/TeamInformationForm.vue'
import UserInformationForm from './Partials/UserInformationForm.vue'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import AccountPageLayout from './AccountPageLayout.vue'

export default {
    components: {
        TeamInformationForm,
        UserInformationForm,
    },

    layout: [AuthLayout, AccountPageLayout],

    props: {
        user: {
            type: Object,
            default: () => ({}),
        },
        profile: {
            type: Object,
            required: true,
        },
        legalForms: {
            required: false,
            type: Array,
            default: () => [],
        },
        institutionTypes: {
            required: false,
            type: Array,
            default: () => [],
        },
        currencies: {
            required: false,
            type: Array,
            default: () => [],
        },
        employees: {
            required: false,
            type: Array,
            default: () => [],
        },
    },
}
</script>
