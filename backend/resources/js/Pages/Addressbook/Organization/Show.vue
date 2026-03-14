<script setup>
import SoleAddressbookToolbar from '../Partials/SoleAddressbookToolbar.vue'
import Subscriber from '@Todos/Partials/Subscriber/Subscriber.vue'
import OrganizationForm from './Partials/OrganizationForm.vue'
import OrganizationMemberForm from './Partials/OrganizationMemberForm.vue'
import ItemActionMenu from '../../../Components/Menus/ItemActionMenu.vue'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import AddressbookLayout from '../AddressbookLayout.vue'
import { computed, ref } from 'vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import CoForm from '../Partials/CoForm.vue'

const { $route, $confirm, $inertia, $root } = useGlobalVariables()
const props = defineProps({
    legalForms: {
        type: Array,
        required: false,
        default: () => [],
    },
    institutionTypes: {
        required: false,
        default: () => [],
        type: Array,
    },
    organization: {
        required: true,
        type: Object,
    },
    members: {
        required: true,
        type: Array,
    },
    positions: {
        required: false,
        type: Array,
        default: () => [],
    },
})

const coForm = ref(null)

const headers = ref([
    { text: 'Name', sortable: false },
    { text: 'Position', sortable: false },
    { text: 'Email', sortable: false },
    { text: 'Phone', sortable: false },
    { text: 'Actions', sortable: false, align: 'right' },
])

const contact = computed(() => props.organization.column_values)

const addressDetails = computed(() => contact.value.address)

const destroy = () => {
    $confirm.ask({
        title: 'Delete',
        question: `Do you want to delete ${props.organization.name} on your address book?`,
        type: 'delete',
        confirm: () => {
            $inertia.delete(
                $route('addressbooks.organizations.destroy', {
                    organization: this.organization,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: () => {
                        $root.$emit(
                            'flash.success',
                            'Organization has been deleted on your address book.',
                        )

                        $inertia.get(
                            this.$route('addressbooks.organizations.index'),
                        )
                    },
                    onError: (errors) =>
                        $root.$emit('flash.validation', errors),
                },
            )
        },
    })
}

const goToMember = (person) => {
    $inertia.get(
        $route('addressbooks.people.show', {
            person,
        }),
        {},
        {
            preserveState: true,
            preserveScroll: true,
        },
    )
}

const removeMember = (member) => {
    $confirm.ask({
        title: 'Delete',
        question: 'Do you want to remove this member from this organization?',
        type: 'delete',
        confirm: () => {
            $inertia.delete(
                $route('addressbooks.organizations.members.destroy', {
                    organization: props.organization,
                    member,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['members', 'errors'],
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
                :name="contact.name"
                :photo="contact.photo"
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
                            icon
                            color="info"
                            small
                            class="ma-0"
                            @click="
                                $refs.contactOrganizationForm.show(organization)
                            "
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
                                <v-flex
                                    grow
                                    xs12
                                >
                                    <v-flex
                                        xs12
                                        class="tw-flex tw-flex-row"
                                    >
                                        <div class="mr-2 subheader">
                                            {{ $t('addressbook.contact') }}:
                                        </div>
                                        <div>
                                            {{ contact.phone ?? '-' }}
                                        </div>
                                    </v-flex>
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
                                            {{
                                                $t('labels.inputs.legal-form')
                                            }}:
                                        </div>
                                        <div>{{ contact.legalform }}</div>
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        class="tw-flex tw-flex-row"
                                    >
                                        <div class="mr-2 subheader">
                                            {{
                                                $t(
                                                    'labels.inputs.institution-type',
                                                )
                                            }}:
                                        </div>
                                        <div>
                                            {{ contact.institution_type }}
                                        </div>
                                    </v-flex>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-text>
                </v-card>
            </v-flex>
            <v-flex
                xs12
                mt-3
            >
                <v-card>
                    <v-card-title
                        primary-title
                        class="pb-2 pt-3"
                    >
                        <div class="subheading">
                            {{
                                $tc(
                                    'addressbook.labels.co_addresses',
                                    members.length,
                                )
                            }}
                        </div>
                        <v-spacer />
                        <v-btn
                            icon
                            color="info"
                            small
                            class="ma-0"
                            @click="$refs.organizationMemberForm.show()"
                        >
                            <v-icon small>add</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-divider />
                    <v-card-text>
                        <v-data-table
                            :headers="headers"
                            :headers-length="headers.length"
                            :items="members"
                            class="elevation-1"
                            hide-actions
                        >
                            <template
                                #items="{
                                    item: {
                                        column_values: {
                                            member,
                                            name,
                                            photo,
                                            email,
                                            phone,
                                        },
                                        id,
                                    },
                                }"
                            >
                                <td
                                    class="tw-flex tw-items-center tw-gap-x-2 text-no-wrap hover:tw-underline tw-cursor-pointer"
                                    @click="goToMember(id)"
                                >
                                    <Subscriber
                                        :avatar="photo"
                                        :name="name"
                                    />
                                    {{ name }}
                                </td>
                                <td>{{ member.position }}</td>
                                <td>{{ email }}</td>
                                <td class="text-no-wrap">
                                    {{ phone ?? '-' }}
                                </td>
                                <td class="tw-text-right">
                                    <v-icon
                                        small
                                        class="mr-2"
                                        @click="
                                            (e) =>
                                                $refs.itemActionMenu.show(e, id)
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
        <OrganizationForm
            ref="contactOrganizationForm"
            :institution-types="institutionTypes"
            :legal-forms="legalForms"
            :request-props="['organization', 'errors']"
        />
        <OrganizationMemberForm
            ref="organizationMemberForm"
            :organization="organization.id"
            :positions="positions"
        />
        <ItemActionMenu
            ref="itemActionMenu"
            @click:details="(member) => goToMember(member)"
            @click:update="
                (member) => $refs.organizationMemberForm.show(member)
            "
            @click:delete="(member) => removeMember(member)"
        />
        <CoForm
            ref="coForm"
            title="C/O Form"
            :members="members"
            :addressbook="organization"
        />
    </div>
</template>
