<template>
    <div>
        <portal
            to="toolbar"
            tag="span"
        >
            <AddressbookToolbar
                slot-scope="{ pages }"
                :pages="pages"
                :initial-search="filters.q"
                :is-loading="isLoading"
                @search="(e) => filterPeople(e)"
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
                    @click="$refs.personForm.show()"
                >
                    <v-icon class="mr-1">add</v-icon>
                    {{
                        $vuetify.breakpoint.xs
                            ? ''
                            : $tc('addressbook.labels.person')
                    }}
                </v-btn>
            </v-flex>
            <v-spacer />
            <v-flex
                tw-text-right
                tw-font-bold
                tw-text-base
            >
                {{ addressbooks.length }}
                {{ $tc('addressbook.labels.person', addressbooks.length) }}
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
                    :items="addressbooks"
                    :loading="isLoading"
                    class="elevation-1"
                    hide-actions
                >
                    <template
                        #items="{
                            item: {
                                column_values: { photo, name, phone, email },
                            },
                            item,
                        }"
                    >
                        <td
                            class="tw-flex tw-items-center tw-gap-x-2 hover:tw-underline tw-cursor-pointer"
                            @click="
                                $inertia.get(
                                    $route('addressbooks.people.show', {
                                        person: item,
                                    }),
                                )
                            "
                        >
                            <Subscriber
                                :avatar="photo"
                                :name="name"
                            />
                            {{ name }}
                        </td>
                        <td>{{ email }}</td>
                        <td>{{ phone }}</td>
                        <td class="tw-text-right">
                            <v-icon
                                small
                                class="mr-2"
                                @click="
                                    (e) => $refs.itemActionMenu.show(e, item)
                                "
                            >
                                more_horiz
                            </v-icon>
                        </td>
                    </template>
                </v-data-table>
            </v-flex>
        </v-layout>
        <PersonForm ref="personForm" />
        <ItemActionMenu
            ref="itemActionMenu"
            @click:update="(person) => $refs.personForm.show(person)"
            @click:delete="(person) => destroy(person)"
            @click:details="
                (person) =>
                    $inertia.get(
                        $route('addressbooks.people.show', {
                            person,
                        }),
                    )
            "
        />
        <v-btn
            dark
            fixed
            bottom
            right
            icon
            large
            color="primary"
            @click="$refs.personForm.show()"
        >
            <v-icon>add</v-icon>
        </v-btn>
    </div>
</template>
<script>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import AddressbookLayout from '../AddressbookLayout.vue'
import Subscriber from '@Todos/Partials/Subscriber/Subscriber.vue'
import PersonForm from './Partials/PersonForm.vue'
import ItemActionMenu from '../../../Components/Menus/ItemActionMenu.vue'
import AddressbookToolbar from '../Partials/AddressbookToolbar.vue'
import { useDebounceFn } from '@vueuse/core'

export default {
    components: {
        Subscriber,
        PersonForm,
        ItemActionMenu,
        AddressbookToolbar,
    },

    layout: [AuthLayout, AddressbookLayout],

    props: {
        addressbooks: {
            type: Array,
            required: true,
        },
        filters: {
            required: false,
            type: [Object, Array],
            default: () => ({}),
        },
    },

    data: () => ({
        isLoading: false,
    }),

    computed: {
        initialFilter() {
            return this.filters?.initial
        },

        headers() {
            return [
                { text: this.$t('labels.inputs.name'), sortable: false },
                { text: this.$t('labels.inputs.email'), sortable: false },
                { text: this.$t('addressbook.phone'), sortable: false },
                {
                    text: this.$t('labels.actions'),
                    sortable: false,
                    align: 'right',
                },
            ]
        },
    },

    created() {
        this.filterPeople = useDebounceFn(async (q) => {
            this.$inertia.reload({
                only: ['addressbooks', 'filters'],
                data: {
                    ...this.filters,
                    q,
                },
                onBefore: () => (this.isLoading = true),
                onFinish: () => (this.isLoading = false),
            })
        }, 500)
    },

    methods: {
        destroy(person) {
            this.$confirm.ask({
                title: this.$t('labels.delete'),
                question: this.$t(
                    'addressbook.messages.person.confirm-deleting',
                ),
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('addressbooks.people.destroy', {
                            person,
                        }),
                        {
                            preserveScroll: true,
                            preserveState: true,
                            only: ['addressbooks', 'filters'],
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    this.$t(
                                        'addressbook.messages.person.deleted',
                                    ),
                                )
                            },
                            onError: (errors) =>
                                this.$root.$emit('flash.validation', errors),
                            onFinish: () =>
                                this.$inertia.get(
                                    this.$route('addressbooks.people.index'),
                                ),
                        },
                    )
                },
            })
        },
    },
}
</script>
