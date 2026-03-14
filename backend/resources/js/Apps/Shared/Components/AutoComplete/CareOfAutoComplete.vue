<script setup>
import api from 'axios'
import { useForm } from '@inertiajs/vue2'
import { inject, ref, watch } from 'vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'

const { $route } = useGlobalVariables()
const form = useForm({
    search: null,
})

const setCareOf = inject('setCareOf')

const isLoading = ref(false)

const search = ref(null)
const items = ref([])

const selected = ref(null)

watch(selected, (value) => setCareOf(value.id))

watch(
    () => form.search,
    async (value) => {
        items.value = []
        const { data } = await api.get($route('json.addressbook.careof'), {
            params: { search: value },
        })
        items.value = data
    },
)
</script>

<template>
    <v-autocomplete
        v-model="selected"
        :search-input.sync="search"
        :loading="isLoading"
        :items="items"
        :no-filter="true"
        prepend-inner-icon="perm_contact_calendar"
        outline
        single-line
        allow-overflow
        hide-selected
        return-object
        placeholder="Care Of"
        color="primary"
        item-text="name"
        @update:searchInput="(q) => (form.search = q)"
    >
        <template #selection="data">
            <slot
                name="selection"
                v-bind="data"
            >
                <v-chip
                    label
                    disabled
                    selected
                >
                    <v-avatar>
                        <img :src="data.item.photo" />
                    </v-avatar>
                    {{ data.item.name }}
                </v-chip>
            </slot>
        </template>
        <template #item="{ item }">
            <slot
                name="item"
                v-bind="item"
            >
                <v-list-tile-avatar>
                    <img :src="item.photo" />
                </v-list-tile-avatar>
                <v-list-tile-content>
                    <v-list-tile-title>
                        {{ item.name }}
                    </v-list-tile-title>
                    <v-list-tile-sub-title>
                        {{ item?.email }}
                    </v-list-tile-sub-title>
                </v-list-tile-content>
            </slot>
        </template>
        <template #no-data>
            <slot name="no-data">
                <v-list-tile
                    avatar
                    ripple
                >
                    <v-list-tile-avatar />
                    <v-list-tile-content>
                        <v-list-tile-title>
                            {{ $t('hints.nothing-found') }}
                        </v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </slot>
        </template>
    </v-autocomplete>
</template>
