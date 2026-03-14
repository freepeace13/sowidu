<script setup>
import Subscriber from '@Todos/Partials/Subscriber/Subscriber.vue'
import ItemActionMenu from '@components/Menus/ItemActionMenu.vue'
import SoleAddressbookToolbar from '../Partials/SoleAddressbookToolbar.vue'
import LinkOrganizationForm from './Partials/LinkOrganizationForm.vue'
import PersonForm from './Partials/PersonForm.vue'
import CoForm from '../Partials/CoForm.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'

const { $inertia, $root, $confirm, $route } = useGlobalVariables()

const props = defineProps({
    addressbook: {
        required: true,
        type: Object,
    },

    organizations: {
        required: true,
        type: Array,
    },

    positions: {
        required: false,
        type: Array,
        default: () => [],
    },
    members: {
        type: Array,
    },
})

const personForm = ref(null)
const coForm = ref(null)
const linkOrganizationForm = ref(null)
const itemActionMenu = ref(null)

const headers = ref([
    { text: 'Name', sortable: false },
    { text: 'Institution Types', sortable: false },
    { text: 'Position', sortable: false },
    { text: 'Actions', sortable: false, align: 'right' },
])

const columnValues = computed(() => props.addressbook.column_values)
const addressDetails = computed(() => columnValues.value.address)

const destroy = () => {
    const person = props.addressbook
    $confirm.ask({
        title: 'Delete',
        question: 'Do you want to delete this person on your addressbook?',
        type: 'delete',
        confirm: () => {
            $inertia.delete(
                $route('addressbooks.people.destroy', {
                    person,
                }),
                {
                    onSuccess: () => {
                        $root.$emit(
                            'flash.success',
                            'Contact person has been deleted.',
                        )

                        $inertia.get($route('addressbooks.people.index'))
                    },
                    onError: (errors) =>
                        $root.$emit('flash.validation', errors),
                },
            )
        },
    })
}

const detachToOrganization = (organization) => {
    const member = props.addressbook

    $confirm.ask({
        title: 'Delete',
        question: 'Do you want to detach this organization from this member?',
        type: 'delete',
        confirm: () => {
            $inertia.delete(
                $route('addressbooks.organizations.members.destroy', {
                    organization,
                    member,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['organizations', 'errors'],
                    onSuccess: () => {
                        $root.$emit(
                            'flash.success',
                            'Member has been removed from this organization.',
                        )
                    },
                },
            )
        },
    })
}
</script>
<script>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import AddressbookLayout from '../AddressbookLayout.vue'
import { computed, ref } from 'vue'

export default {
    layout: [AuthLayout, AddressbookLayout],
}
</script>
<template>
    <div>
        <portal
            to="toolbar"
            tag="span"
        >
            <SoleAddressbookToolbar
                :name="columnValues.name"
                :photo="columnValues.photo"
                @click:delete="destroy"
            />
        </portal>
        <v-layout
            align-start
            row
            wrap
            mt-2
        >
            <v-flex xs12>
                <v-card>
                    <v-card-title
                        primary-title
                        class="pb-2 pt-3"
                    >
                        <div class="subheading">{{ $t('labels.details') }}</div>
                        <v-spacer />
                        <v-btn
                            flat
                            icon
                            color="info"
                            small
                            class="ma-0"
                            @click="$refs.personForm.show(addressbook)"
                        >
                            <v-icon small>edit</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-divider />
                    <v-card-text>
                        <v-container
                            grid-list-md
                            fluid
                            pa-0
                        >
                            <v-layout
                                row
                                wrap
                            >
                                <v-flex xs12>
                                    <v-flex
                                        xs12
                                        class="tw-flex tw-flex-row"
                                    >
                                        <div class="mr-2 subheader">
                                            {{ $t('addressbook.address') }}:
                                        </div>
                                        <div>
                                            {{ addressDetails.full }}
                                        </div>
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        class="tw-flex tw-flex-row"
                                    >
                                        <div class="mr-2 subheader">
                                            {{ $t('addressbook.phone') }}:
                                        </div>
                                        <div>
                                            {{
                                                columnValues?.phone ?? 'Not set'
                                            }}
                                        </div>
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        class="tw-flex tw-flex-row"
                                    >
                                        <div class="mr-2 subheader">Email:</div>
                                        <a
                                            :href="`mailto:${columnValues.email}`"
                                        >
                                            {{ columnValues.email }}
                                        </a>
                                    </v-flex>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-text>
                </v-card>
            </v-flex>
            <v-flex xs12>
                <v-card>
                    <v-card-title
                        primary-title
                        class="pb-2 pt-3"
                    >
                        <div class="subheading">
                            {{ $tc('addressbook.labels.co_addresses', 2) }}
                        </div>
                        <v-spacer />
                        <v-btn
                            icon
                            color="info"
                            small
                            class="ma-0"
                            @click="$refs.linkOrganizationForm.show()"
                        >
                            <v-icon small>add</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-divider />
                    <v-card-text>
                        <v-data-table
                            :headers="headers"
                            :headers-length="headers.length"
                            :items="organizations"
                            class="elevation-1"
                            hide-actions
                        >
                            <template
                                #items="{
                                    item: { column_values: organization },
                                    item,
                                }"
                            >
                                <td
                                    class="tw-flex tw-items-center tw-gap-x-2 tw-whitespace-nowrap tw-w-[1%] font-weight-bold tw-cursor-pointer hover:tw-underline"
                                    @click="
                                        $inertia.visit(
                                            $route(
                                                'addressbooks.organizations.show',
                                                {
                                                    organization,
                                                },
                                            ),
                                        )
                                    "
                                >
                                    <Subscriber
                                        :name="organization.name"
                                        :avatar="organization.photo"
                                    />
                                    {{ organization.name }}
                                </td>
                                <td>{{ organization.institution_type }}</td>
                                <td>{{ organization.member.position }}</td>
                                <td class="tw-text-right">
                                    <v-icon
                                        small
                                        class="mr-2"
                                        @click="
                                            (e) =>
                                                $refs.itemActionMenu.show(
                                                    e,
                                                    item,
                                                )
                                        "
                                    >
                                        more_horiz
                                    </v-icon>
                                </td>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
        <PersonForm
            ref="personForm"
            :request-props="['addressbook', 'errors']"
        />
        <LinkOrganizationForm
            ref="linkOrganizationForm"
            :person="addressbook"
            :positions="positions"
        />
        <CoForm
            ref="coForm"
            title="C/O Form"
            :members="organizations"
            :addressbook="addressbook"
        />
        <ItemActionMenu
            ref="itemActionMenu"
            @click:update="
                (organization) => $refs.linkOrganizationForm.show(organization)
            "
            @click:delete="(organization) => detachToOrganization(organization)"
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
