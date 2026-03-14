<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { router, useForm } from '@inertiajs/vue2'
import { watchDebounced } from '@vueuse/core'

const props = defineProps({
    users: {
        required: true,
        type: Array,
    },
    pagination: {
        required: true,
        type: Object,
    },
    filters: {
        required: true,
        type: Object,
    },
})

const { $t, $route, $confirm } = useGlobalVariables()

const headers = [
    {
        text: $t('labels.inputs.name'),
        sortable: false,
    },
    {
        text: $t('labels.inputs.email'),
        sortable: false,
    },
    {
        text: $t('app_settings.users-manager.labels.email-verified'),
        sortable: false,
        align: 'center',
    },
    {
        text: $t('app_settings.users-manager.labels.allow-access'),
        sortable: false,
        align: 'center',
    },
    {
        text: $t('labels.actions'),
        sortable: false,
        align: 'right',
    },
]

const filter = useForm({
    search: props.filters.search,
})

watchDebounced(
    () => filter.isDirty,
    (isDirty) => {
        if (isDirty) {
            fetch(1)
        }
    },
    { debounce: 500 },
)

function fetch(page = 1) {
    filter
        .transform((data) => ({
            ...data,
            page,
        }))
        .get($route('app.settings.manager.users'), {
            preserveScroll: true,
            preserveState: true,
        })
}

function confirmBlocking(user) {
    $confirm({
        title: $t('app_settings.users-manager.labels.confirm-block-user'),
        question:
            $t('app_settings.users-manager.labels.confirm-block-user-name') +
            ' ' +
            user.name,
        confirmText: $t('app_settings.users-manager.labels.block'),
        type: 'delete',
        confirm: () =>
            router.delete(
                $route('app.settings.manager.users.block', { user }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['users'],
                },
            ),
    })
}
</script>
<template>
    <v-container
        fluid
        grid-list-md
    >
        <v-layout
            row
            wrap
            align-center
            justify-space-between
        >
            <v-flex
                xs12
                class="headline shrink font-weight-bold mb-3"
            >
                <div class="tw-flex tw-items-center">
                    <v-btn
                        flat
                        icon
                        @click="router.get($route('app.settings.index'))"
                    >
                        <v-icon>arrow_back</v-icon>
                    </v-btn>
                    <div>
                        {{ $t('app_settings.labels.users-manager') }}
                    </div>
                </div>
                <v-divider />
            </v-flex>
            <v-flex xs6>
                <div class="tw-flex tw-justify-between">
                    <v-text-field
                        v-model="filter.search"
                        hide-details
                        :placeholder="$t('labels.search')"
                        color="primary"
                        solo
                        flat
                        clearable
                        prepend-inner-icon="search"
                        :loading="filter.processing"
                        @click:clear="() => (filter.search = '')"
                    />
                </div>
            </v-flex>
            <v-flex
                xs6
                class="tw-text-right tw-self-end"
            >
                Showing
                {{
                    pagination?.per_page > pagination?.total
                        ? pagination.total
                        : pagination.per_page ?? 0
                }}
                of {{ pagination?.total ?? 0 }} results.
            </v-flex>

            <v-flex
                xs12
                mt-3
            >
                <v-data-table
                    :headers="headers"
                    :items="users"
                    class="elevation-5"
                    hide-actions
                    item-key="id"
                    :loading="filter.processing"
                >
                    <template #items="{ item: user }">
                        <tr>
                            <td class="">
                                {{ user.name }}
                            </td>
                            <td class="tw-italic">
                                {{ user.email }}
                            </td>
                            <td class="tw-text-center">
                                <v-icon
                                    :color="user.verified ? 'success' : 'error'"
                                >
                                    {{
                                        user.verified
                                            ? 'check_circle'
                                            : 'cancel'
                                    }}
                                </v-icon>
                            </td>
                            <td class="tw-text-center">
                                <v-icon
                                    :color="
                                        !user.block_access ? 'success' : 'error'
                                    "
                                >
                                    {{
                                        !user.block_access
                                            ? 'check_circle'
                                            : 'cancel'
                                    }}
                                </v-icon>
                            </td>
                            <td class="tw-flex tw-justify-end">
                                <v-btn
                                    flat
                                    icon
                                    fab
                                    color="error"
                                    class="x-small"
                                    @click="confirmBlocking(user)"
                                >
                                    <v-icon
                                        dark
                                        small
                                    >
                                        lock_person
                                    </v-icon>
                                </v-btn>
                            </td>
                        </tr>
                    </template>
                </v-data-table>
                <div class="text-xs-center pt-2">
                    <v-pagination
                        :value="pagination.current_page"
                        :length="pagination.total_page"
                        @input="(page) => fetch(page)"
                    />
                </div>
            </v-flex>
        </v-layout>
    </v-container>
</template>
