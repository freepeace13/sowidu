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
                                column_values: { photo, name },
                            },
                            item,
                        }"
                    >
                        <td class="tw-flex tw-items-center tw-gap-x-2">
                            <AppAvatar
                                :avatar="photo"
                                :name="name"
                            />
                            {{ name }}
                        </td>
                        <td class="tw-capitalize">{{ item.type }}</td>
                        <td class="tw-text-right">
                            <v-icon
                                small
                                class="mr-2"
                                @click="(e) => moreButtonIsClicked(e, item)"
                            >
                                more_horiz
                            </v-icon>
                        </td>
                    </template>
                </v-data-table>
            </v-flex>
        </v-layout>
        <ActionMenu
            ref="itemActionMenu"
            nudge-right="16"
            close-on-content-click
            min-width="200"
            left
            bottom
            offset-y
            :items="[
                {
                    icon: 'restore_from_trash',
                    name: 'restore',
                    action: 'click:restore',
                },
                {
                    icon: 'delete_forever',
                    name: 'delete',
                    action: 'click:destroy',
                },
            ]"
            @click:restore="(addressbook) => restore(addressbook)"
            @click:destroy="(addressbook) => destroy(addressbook)"
        />
    </div>
</template>
<script>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import AddressbookLayout from '../AddressbookLayout.vue'
import AddressbookToolbar from '../Partials/AddressbookToolbar.vue'
import { useDebounceFn } from '@vueuse/core'
import AppAvatar from '@components/AppAvatar.vue'
import ActionMenu from '@components/Menus/ActionMenu.vue'

export default {
    components: {
        AddressbookToolbar,
        AppAvatar,
        ActionMenu,
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
                {
                    text: this.$t('addressbook.labels.contact-type'),
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
        destroy(addressbook) {
            this.$confirm.ask({
                title: this.$t('labels.delete'),
                question: this.$t(
                    'addressbook.messages.trash.confirm-deleting',
                ),
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('addressbooks.trashes.destroy', {
                            addressbook,
                        }),
                        {
                            preserveScroll: true,
                            preserveState: true,
                            only: ['addressbooks', 'filters'],
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    this.$t(
                                        'addressbook.messages.trash.deleted',
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
        restore(addressbook) {
            this.$confirm.ask({
                title: this.$t('buttons.restore'),
                question: this.$t('addressbook.messages.trash.confirm-restore'),
                type: 'info',
                confirm: () => {
                    this.$inertia.put(
                        this.$route('addressbooks.trashes.restore', {
                            addressbook,
                        }),
                        {
                            preserveState: true,
                            preserveScroll: true,
                            only: ['addressbooks'],
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    this.$t(
                                        'addressbook.messages.trash.restored',
                                    ),
                                )
                            },
                        },
                    )
                },
            })
        },

        moreButtonIsClicked(e, item) {
            e.preventDefault()
            this.$refs.itemActionMenu.show(
                {
                    x: e.clientX,
                    y: e.clientY,
                },
                item,
            )
        },
    },
}
</script>
