<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { router } from '@inertiajs/vue2'
import { watchDebounced } from '@vueuse/core'
import { reactive, ref } from 'vue'
import TranslationForm from './Components/TranslationForm.vue'

const props = defineProps({
    translationList: {
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

const { $t } = useGlobalVariables()
const translationFormRef = ref()
const isLoading = ref(false)

const headers = [
    {
        text: $t('app_settings.translation-manager.labels.translation-keys'),
        sortable: false,
    },
    {
        text: $t('app_settings.translation-manager.labels.en-value'),
        sortable: false,
        width: '30%',
    },
    {
        text: $t('app_settings.translation-manager.labels.de-value'),
        sortable: false,
        width: '30%',
    },
    {
        text: $t('labels.actions'),
        sortable: false,
        align: 'right',
    },
]

const form = reactive({
    q: props.filters.q,
})

watchDebounced(
    () => form.q,
    () => {
        fetch(1)
    },
    { debounce: 300 },
)

function fetch(page = 1) {
    router.reload({
        data: {
            page,
            q: form.q,
        },
        only: ['translationList', 'pagination', 'filters'],
        preserveScroll: true,
        preserveState: true,
    })
}
</script>
<template>
    <v-container
        fluid
        grid-list-md
    >
        <TranslationForm ref="translationFormRef" />
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
                        {{ $t('app_settings.labels.translation-manager') }}
                    </div>
                </div>
                <v-divider />
            </v-flex>
            <v-flex xs6>
                <div class="tw-flex tw-justify-between">
                    <v-text-field
                        v-model="form.q"
                        hide-details
                        :placeholder="$t('labels.search')"
                        color="primary"
                        solo
                        flat
                        clearable
                        prepend-inner-icon="search"
                        :loading="isLoading"
                        @click:clear="() => (form.q = '')"
                    />
                </div>
            </v-flex>
            <v-flex
                xs6
                class="tw-text-right tw-self-end"
            >
                Showing {{ pagination?.per_page ?? 0 }} of
                {{ pagination?.total ?? 0 }} results.
            </v-flex>

            <v-flex
                xs12
                mt-3
            >
                <v-data-table
                    :headers="headers"
                    :items="translationList"
                    class="elevation-5"
                    hide-actions
                    item-key="id"
                    :loading="isLoading"
                >
                    <template #items="{ item: translation }">
                        <tr>
                            <td class="!tw-font-semibold">
                                {{ translation.key }}
                            </td>
                            <td class="!tw-relative">
                                <div class="tw-italic truncate">
                                    {{ translation.value }}
                                </div>
                            </td>
                            <td class="!tw-relative">
                                <div class="tw-italic truncate">
                                    {{ translation?.de ?? '--' }}
                                </div>
                            </td>
                            <td class="tw-flex tw-justify-end">
                                <v-btn
                                    dark
                                    small
                                    color="info"
                                    @click="
                                        $refs.translationFormRef.show(
                                            translation,
                                        )
                                    "
                                >
                                    <v-icon
                                        small
                                        dark
                                        left
                                    >
                                        edit
                                    </v-icon>
                                    {{ $t('buttons.update') }}
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
<style scoped>
.truncate {
    top: 30%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    position: absolute;
    max-width: 95%;
}
</style>
