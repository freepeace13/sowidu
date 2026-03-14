<script setup>
import SearchInput from '../../../../Components/Fields/SearchInput.vue'
import { useDebounceFn } from '@vueuse/core'
import AddressFields from '@components/Fields/AddressFields.vue'
import { useShowAddressbook } from '@/Composables/useAddressbook'
import { useNewPersonOrganizationSearch } from '@/Apps/Addressbook/Composables/Person'
import { useForm, router } from '@inertiajs/vue2'
import { computed, onMounted, ref, watch } from 'vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'

const { $root, $route } = useGlobalVariables()

const props = defineProps({
    person: {
        required: true,
        type: Object,
    },

    positions: {
        required: true,
        type: Array,
    },
})

const form = useForm({
    position: null,
    phone: null,
})

const organization = ref({
    address: {
        house_number: null,
        street: null,
        zipcode: null,
        city: null,
        state: null,
        country: null,
    },
})

const selected = ref(null)
const search = ref({
    isLoading: false,
    items: [],
})

const isShow = ref(false)
const isFetchingAddressbook = ref(false)
const isEditing = ref(false)
const searchOrganization = ref([])
const hasChosenAnOrganization = computed(() => {
    return !!selected.value
})

watch(
    selected,
    (value) => {
        if (!value) return

        fetchOrganizationAddressbook(value)
    },
    { deep: true },
)

const fetchMemberAddressbook = async ({ id: organization }) => {
    try {
        isFetchingAddressbook.value = true
        const {
            data: { position },
        } = await useShowAddressbook(props.person.id, {
            organization,
        })

        form.position = position
    } catch (error) {
        $root.$emit('flash.error', error)
    } finally {
        isFetchingAddressbook.value = false
    }
}

onMounted(() => {
    searchOrganization.value = useDebounceFn(async (keyword) => {
        try {
            if (!keyword) return

            search.value.isLoading = true
            search.value.items = []

            const { data } = await useNewPersonOrganizationSearch(
                props.person.id,
                keyword,
                5,
            )

            search.value.items = data
        } catch (error) {
            $root.$emit('flash.error', error)
        } finally {
            search.value.isLoading = false
        }
    }, 500)
})

const show = (selection = null) => {
    reset()
    router.reload({ only: ['positions'] })

    if (selection) {
        isEditing.value = true
        selected.value = selection
        fetchMemberAddressbook(selection)
    }

    isShow.value = true
}

const fetchOrganizationAddressbook = async (selection) => {
    console.log(selection)
    try {
        isFetchingAddressbook.value = true

        const { data } = await useShowAddressbook(selection)
        organization.value = data
    } catch (error) {
        $root.$emit('flash.error', error)
    } finally {
        isFetchingAddressbook.value = false
    }
}

const close = () => {
    isShow.value = false
    reset()
}

const reset = () => {
    form.reset()
    isFetchingAddressbook.value = false
    organization.value = {
        address: {
            house_number: null,
            street: null,
            zipcode: null,
            city: null,
            state: null,
            country: null,
        },
    }
    selected.value = null
    isEditing.value = false
}
const create = () => {
    const {
        id: addressbook_id,
        column_values: { email, address, name, photo, first_name, last_name },
    } = props.person

    if (!addressbook_id) return

    form.transform((data) => ({
        ...data,
        addressbook_id,
        address: {
            ...address,
            country: address.country?.code,
        },
        name,
        photo,
        email,
        first_name,
        last_name,
    })).post(
        $route('addressbooks.organizations.members.store', {
            organization: organization.value.id,
        }),
        {
            preserveState: true,
            preserveScroll: true,
            only: ['members', 'errors', 'organizations'],
            onSuccess: () => {
                $root.$emit(
                    'flash.success',
                    'New organization has been added to this contact person.',
                )
                close()
            },
            onError: (errors) => $root.$emit('flash.validation', errors),
        },
    )
}

const update = () => {
    const {
        column_values: { address, name, photo },
        id: member,
    } = props.person

    form.transform((data) => ({
        ...data,
        address: {
            ...address,
            country: address.country?.code,
        },
        name,
        photo,
    })).put(
        this.$route('addressbooks.organizations.members.update', {
            organization: organization.value,
            member,
        }),
        {
            preserveState: true,
            preserveScroll: true,
            only: ['members', 'errors', 'organizations'],
            onSuccess: () => {
                $root.$emit(
                    'flash.success',
                    'Organization member has been updated.',
                )
                close()
            },
            onError: (errors) => $root.$emit('flash.validation', errors),
        },
    )
}

const save = () => {
    if (isEditing.value) return update()

    return create()
}

defineExpose({
    show,
    close,
})
</script>

<template>
    <v-dialog
        v-model="isShow"
        persistent
        max-width="600px"
        lazy
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{
                        $t('messages.link-to', {
                            organization: $t('addressbook.labels.organization'),
                        })
                    }}
                </v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="close"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-divider />
            <v-card-text>
                <v-container
                    grid-list-lg
                    fluid
                    pa-2
                >
                    <v-layout column>
                        <v-flex
                            v-if="!isEditing"
                            xs12
                        >
                            <SearchInput
                                v-model="selected"
                                :items="search.items"
                                :is-loading="search.isLoading"
                                :use-addressbook="true"
                                placeholder="Search organization"
                                @update:search-input="searchOrganization"
                            >
                                <template #item="item">
                                    <v-list-tile-avatar>
                                        <img :src="item.photo" />
                                    </v-list-tile-avatar>
                                    <v-list-tile-content>
                                        <v-list-tile-title>
                                            {{ item.name }}
                                        </v-list-tile-title>
                                        <v-list-tile-sub-title>
                                            {{ item.institution_type }}
                                        </v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </template>
                            </SearchInput>
                        </v-flex>
                        <v-flex v-if="isEditing">
                            <v-text-field
                                :value="organization?.name"
                                color="primary"
                                :loading="form.processing"
                                :disabled="form.processing"
                                readonly
                                label="Organization name"
                                outline
                                hide-details
                                required
                            />
                        </v-flex>
                        <v-expand-transition>
                            <div v-show="hasChosenAnOrganization">
                                <v-flex xs12>
                                    <v-combobox
                                        v-model="form.position"
                                        :items="positions"
                                        :loading="
                                            form.processing ||
                                            isFetchingAddressbook
                                        "
                                        :disabled="
                                            form.processing ||
                                            isFetchingAddressbook
                                        "
                                        :hide-details="!form.errors.position"
                                        :error-messages="form.errors.position"
                                        :label="$t('addressbook.position')"
                                        :placeholder="
                                            $t('select-or-search-for-position')
                                        "
                                        color="primary"
                                        outline
                                    />
                                </v-flex>
                                <v-divider />
                                <v-expansion-panel
                                    px-0
                                    class="tw-shadow-none"
                                >
                                    <v-expansion-panel-content>
                                        <template #header>
                                            <div
                                                class="tw-cursor-pointer info--text"
                                            >
                                                {{
                                                    $tc(
                                                        'addressbook.labels.organization',
                                                    )
                                                }}
                                                {{
                                                    $t(
                                                        'addressbook.labels.contact_details',
                                                    )
                                                }}
                                            </div>
                                        </template>
                                        <v-flex xs12>
                                            <v-text-field
                                                :value="organization?.phone"
                                                color="primary"
                                                readonly
                                                :loading="form.processing"
                                                label="Phone"
                                                outline
                                                hide-details
                                            />
                                        </v-flex>
                                        <v-subheader class="subheading mt-2">
                                            {{
                                                $t('addressbook.labels.address')
                                            }}
                                        </v-subheader>

                                        <v-layout column>
                                            <v-flex
                                                xs12
                                                px-3
                                            >
                                                <AddressFields
                                                    v-model="
                                                        organization.address
                                                    "
                                                    :is-loading="
                                                        form.processing
                                                    "
                                                    readonly
                                                />
                                            </v-flex>
                                        </v-layout>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </div>
                        </v-expand-transition>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-divider />
            <v-card-actions class="px-4">
                <v-btn
                    :loading="form.processing"
                    :disabled="form.processing"
                    color="secondary"
                    depressed
                    @click="close"
                >
                    {{ $t('buttons.cancel') }}
                </v-btn>
                <v-spacer />
                <v-btn
                    color="primary"
                    depressed
                    :loading="form.processing"
                    :disabled="form.processing || !hasChosenAnOrganization"
                    @click="save"
                >
                    {{ $t('buttons.save') }}
                    <template #loader>
                        <span>{{ $t('buttons.saving') }}</span>
                    </template>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
