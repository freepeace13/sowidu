<template>
    <div>
        <portal
            to="toolbar"
            tag="span"
        >
            <AddressbookToolbar
                slot-scope="{ pages }"
                :pages="pages"
                :is-loading="isLoading"
                :initial-search="filters.q"
                @search="(e) => filterOrganization(e)"
            />
        </portal>

        <v-layout
            row
            wrap
            align-center
        >
            <v-flex>
                <v-btn
                    color="primary"
                    :small="$vuetify.breakpoint.xsOnly"
                    @click="$refs.contactOrganizationForm.show()"
                >
                    <v-icon>add</v-icon>
                    {{
                        $vuetify.breakpoint.xs ? '' : $t('labels.organization')
                    }}
                </v-btn>
            </v-flex>
            <v-spacer />
            <v-flex
                tw-text-right
                tw-font-bold
                tw-text-base
            >
                {{ organizations.length }}
                {{
                    $tc('addressbook.labels.organization', organizations.length)
                }}
            </v-flex>
        </v-layout>
        <v-layout
            align-start
            column
        >
            <v-flex
                xs12
                grow
                w-full
                mt-2
            >
                <v-data-table
                    :headers="headers"
                    :headers-length="headers.length"
                    :items="organizations"
                    :loading="isLoading"
                    class="elevation-1"
                    hide-actions
                >
                    <template
                        #items="{ item: organization, item: { column_values } }"
                    >
                        <td
                            class="tw-whitespace-nowrap tw-w-[1%] font-weight-bold tw-cursor-pointer hover:tw-underline"
                            @click="
                                $inertia.visit(
                                    $route('addressbooks.organizations.show', {
                                        organization,
                                    }),
                                )
                            "
                        >
                            {{ column_values.name }}
                        </td>
                        <td>{{ column_values.legalform ?? '-' }}</td>
                        <td>{{ column_values.institution_type }}</td>
                        <td>{{ organization.members_count }}</td>
                        <td class="tw-text-right">
                            <v-icon
                                small
                                class="mr-2"
                                @click="
                                    (e) =>
                                        $refs.itemActionMenu.show(
                                            e,
                                            organization,
                                        )
                                "
                            >
                                more_horiz
                            </v-icon>
                        </td>
                    </template>
                </v-data-table>
            </v-flex>
        </v-layout>

        <v-btn
            dark
            fixed
            bottom
            right
            icon
            large
            color="primary"
            @click="$refs.contactOrganizationForm.show()"
        >
            <v-icon>add</v-icon>
        </v-btn>

        <OrganizationForm
            ref="contactOrganizationForm"
            :legal-forms="legalForms"
            :institution-types="institutionTypes"
        />

        <ItemActionMenu
            ref="itemActionMenu"
            @click:update="
                (organization) =>
                    $refs.contactOrganizationForm.show(organization)
            "
            @click:delete="(organization) => destroy(organization)"
            @click:details="
                (organization) =>
                    $inertia.get(
                        $route('addressbooks.organizations.show', {
                            organization,
                        }),
                    )
            "
        />
    </div>
</template>
<script>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import AddressbookLayout from '../AddressbookLayout.vue'
import OrganizationForm from './Partials/OrganizationForm.vue'
import AddressbookToolbar from '../Partials/AddressbookToolbar.vue'
import ItemActionMenu from '../../../Components/Menus/ItemActionMenu.vue'
import { useDebounceFn } from '@vueuse/shared'

export default {
    components: {
        OrganizationForm,
        AddressbookToolbar,
        ItemActionMenu,
    },

    layout: [AuthLayout, AddressbookLayout],

    props: {
        legalForms: {
            type: Array,
            required: false,
            default: () => [],
        },

        institutionTypes: {
            required: false,
            type: Array,
            default: () => [],
        },

        organizations: {
            required: true,
            type: Array,
        },

        filters: {
            required: true,
            type: [Object, Array],
            default: () => ({}),
        },
    },

    data: () => ({
        isLoading: false,
    }),

    computed: {
        page() {
            return this.filters?.initial
        },

        headers() {
            return [
                { text: this.$t('labels.inputs.name'), sortable: false },
                { text: this.$t('labels.inputs.legal-form'), sortable: false },
                {
                    text: this.$t('labels.inputs.institution-type'),
                    sortable: false,
                },
                {
                    text: this.$tc('labels.employees'),
                    sortable: false,
                },
                {
                    text: this.$t('labels.actions'),
                    sortable: false,
                    align: 'right',
                },
            ]
        },
    },

    created() {
        this.filterOrganization = useDebounceFn(async (q) => {
            this.$inertia.reload({
                only: ['organizations', 'filters'],
                preserveState: true,
                preserveScroll: true,
                data: {
                    ...this.filters,
                    q,
                },
                onBefore: () => (this.isLoading = true),
                onFinish: () => (this.isLoading = false),
            })
        })
    },

    methods: {
        destroy(organization) {
            this.$confirm.ask({
                title: this.$t('labels.delete'),
                question: this.$t(
                    'addressbook.messages.organization.confirm-deleting',
                ),
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('addressbooks.organizations.destroy', {
                            organization,
                        }),
                        {
                            preserveState: true,
                            preserveScroll: true,
                            only: ['organizations', 'errors', 'filters'],
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    this.$t(
                                        'addressbook.messages.organization.deleted',
                                    ),
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
