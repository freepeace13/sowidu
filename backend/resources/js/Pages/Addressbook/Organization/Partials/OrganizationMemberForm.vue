<script setup>
import SearchInput from '../../../../Components/Fields/SearchInput.vue'
import AddressFields from '@components/Fields/AddressFields.vue'
import { useNewOrganizationMemberSearch } from '@/Apps/Addressbook/Composables/Organization'
import { useShowAddressbook } from '@/Composables/useAddressbook'
import { router, useForm } from '@inertiajs/vue2'
import { computed, ref, watch, nextTick } from 'vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'

const { $root, $route } = useGlobalVariables()

const props = defineProps({
    organization: {
        required: true,
        type: Number,
    },

    positions: {
        required: true,
        type: Array,
    },
})
const form = useForm({
    position: null,
    phone: null,
    name: null,
    email: null,
    photo: null,
    first_name: null,
    last_name: null,
    address: {
        house_number: null,
        street: null,
        zipcode: null,
        city: null,
        state: null,
        country: null,
    },
})

const person = ref(null)
const selected = ref(null)
const search = ref({ isLoading: false, items: [] })
const isShow = ref(false)
const isEditing = ref(false)
const isFetchingAddressbook = ref(false)
const hasChosenPerson = computed(() => person.value)

const fetchPersonAddressbook = async (value) => {
    try {
        isFetchingAddressbook.value = true

        const { data } = await useShowAddressbook(value)

        person.value = data
        setMember(data)
    } catch (error) {
        $root.$emit('flash.error', error)
    } finally {
        isFetchingAddressbook.value = false
    }
}

watch(
    selected,
    (value) => {
        if (!value) return

        fetchPersonAddressbook(value)
    },
    { deep: true },
)

const searchPerson = async (keyword) => {
    try {
        if (!keyword) return

        search.value.isLoading = true
        search.value.items = []

        const { data } = await useNewOrganizationMemberSearch(
            props.organization,
            keyword,
            5,
        )

        search.value.items = data
    } catch (error) {
        $root.$emit('flash.error', error)
    } finally {
        search.value.isLoading = false
    }
}

const fetchMemberAddressbook = async (member) => {
    try {
        isFetchingAddressbook.value = true
        const { data } = await useShowAddressbook(member, {
            organization: props.organization,
        })
        setMember(data)
        person.value = data
    } catch (error) {
        $root.$emit('flash.error', error)
    } finally {
        isFetchingAddressbook.value = false
    }
}

const show = (member = null) => {
    reset()

    router.reload({ only: ['positions'] })

    if (member) {
        isEditing.value = true
        fetchMemberAddressbook(member)
    }

    isShow.value = true
}

const setMember = (value) => {
    const {
        name,
        photo,
        phone,
        first_name,
        last_name,
        email,
        address: { full, ...address },
    } = value
    form.first_name = first_name
    form.last_name = last_name
    form.name = name
    form.photo = photo
    form.phone = phone
    form.email = email
    form.address = address
    form.position = value?.position
}

const close = () => {
    isShow.value = false
    reset()
}
const reset = () => {
    form.reset()
    form.clearErrors()
    isEditing.value = false
    isFetchingAddressbook.value = false
    person.value = null
    selected.value = null
}

const create = () => {
    const addressbook_id = person.value?.id

    if (!addressbook_id) return

    form.transform((data) => ({
        ...data,
        address: {
            ...data.address,
            country: data.address.country?.code,
        },
        addressbook_id,
    })).post(
        $route('addressbooks.organizations.members.store', {
            organization: props.organization,
        }),
        {
            preserveState: true,
            preserveScroll: true,
            only: ['members', 'errors'],
            onSuccess: () => {
                $root.$emit(
                    'flash.success',
                    'Organization member has been added on your Address Book.',
                )
                close()
            },
            onError: (errors) => $root.$emit('flash.validation', errors),
        },
    )
}

const update = () => {
    const member = person.value?.id

    form.transform((data) => ({
        ...data,
        address: {
            ...data.address,
            country: data.address.country?.code,
        },
    })).put(
        $route('addressbooks.organizations.members.update', {
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
                    'Organization member has been updated.',
                )
                close()
            },
            onError: (errors) => $root.$emit('flash.validation', errors),
        },
    )
}
const save = () => {
    nextTick(() => {
        if (isEditing.value) return update()

        return create()
    })
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
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ isEditing ? 'Update' : 'Add' }}
                    {{ $t('addressbook.labels.member') }}
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
                                placeholder="Search person or organization"
                                @update:search-input="searchPerson"
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
                                            {{ item.email }}
                                        </v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </template>
                            </SearchInput>
                        </v-flex>
                        <v-divider
                            v-show="!isEditing"
                            class="my-2"
                        />
                        <v-expand-transition>
                            <div v-show="hasChosenPerson || isEditing">
                                <div v-if="isEditing">
                                    <v-flex>
                                        <v-text-field
                                            :value="person.name"
                                            color="primary"
                                            readonly
                                            disabled
                                            :loading="form.processing"
                                            label="Name"
                                            outline
                                            hide-details
                                            required
                                        />
                                    </v-flex>
                                </div>
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
                                        label="Position"
                                        placeholder="Select or search for Position"
                                        color="primary"
                                        outline
                                    />
                                </v-flex>
                                <v-divider class="my-2" />
                                <v-expansion-panel
                                    px-0
                                    class="tw-shadow-none"
                                >
                                    <v-expansion-panel-content>
                                        <template #header>
                                            <div
                                                class="tw-cursor-pointer info--text"
                                            >
                                                Contact Details
                                            </div>
                                        </template>
                                        <v-flex xs12>
                                            <v-text-field
                                                v-model="form.phone"
                                                color="primary"
                                                :loading="form.processing"
                                                :disabled="form.processing"
                                                :error-messages="
                                                    form.errors.phone
                                                "
                                                label="Phone"
                                                autofocus
                                                outline
                                                :hide-details="
                                                    !form.errors.phone
                                                "
                                                required
                                            />
                                        </v-flex>
                                        <v-subheader class="subheading mt-2">
                                            Address
                                        </v-subheader>

                                        <v-layout column>
                                            <v-flex
                                                xs12
                                                px-3
                                            >
                                                <AddressFields
                                                    v-model="form.address"
                                                    :is-loading="
                                                        form.processing
                                                    "
                                                    :errors="form.errors"
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
                    :disabled="form.processing"
                    color="secondary"
                    depressed
                    @click="close"
                >
                    Cancel
                </v-btn>
                <v-spacer />
                <v-btn
                    color="primary"
                    depressed
                    :disabled="form.processing"
                    :loading="form.processing"
                    @click="save"
                >
                    Save
                    <template #loader>
                        <span>Saving...</span>
                    </template>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
