<script setup>
import FileAttachmentFormMenu from '@/Components/Fields/File/FileAttachmentFormMenu.vue'
import MediaDrawer from '@/Components/Media/MediaDrawer.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { getPageProps, isNotEmpty } from '@/Composables/useUtils'
import JumboUploadButton from '@/Pages/Order/Files/Components/JumboUploadButton.vue'
import SubmitButton from '@components/Forms/SubmitButton.vue'
import { useForm } from '@inertiajs/vue2'
import { computed, nextTick, ref } from 'vue'

defineExpose({ show })
const emit = defineEmits(['refresh'])

const { $t, $root, $route } = useGlobalVariables()

const form = useForm({
    name: null,
    type: null,
    internal_id: null,
    vendor_id: null,
    manufacture_id: null,
    unit: null,
    purchasing_price: null,
    selling_price: null,
    description: null,
    media: null,
})

const isShow = ref(false)
const item = ref(null)
const searchType = ref('')
const itemTypes = ref([])

const isCreating = computed(() => !item.value)

const loadingText = computed(() => {
    return isCreating.value ? $t('buttons.creating') : $t('buttons.updating')
})

const unitOptions = computed(() => getPageProps('unitOptions'))
const currency = computed(() => getPageProps('currency'))

function show(catalogItem = null, options = {}) {
    form.reset()
    itemTypes.value = getPageProps('itemTypeOptions')

    if (catalogItem) {
        item.value = catalogItem

        form.name = catalogItem.name
        form.internal_id = catalogItem.internal_id
        form.vendor_id = catalogItem.vendor_id
        form.manufacture_id = catalogItem.manufacture_id
        form.unit = catalogItem.unit
        form.purchasing_price = catalogItem.purchasing_price
        form.selling_price = catalogItem.selling_price
        form.description = catalogItem.description

        const match = itemTypes.value.find(
            (i) => i.value === catalogItem.type.id,
        )
        form.type = match ? match.text : ''

        form.media = catalogItem.media
    }

    if (!catalogItem && isNotEmpty(options)) {
        form.name = options?.name
    }

    isShow.value = true
}

function close() {
    isShow.value = false
    reset()
}

function reset() {
    item.value = null
    searchType.value = ''
    form.reset()
    form.clearErrors()
}

function submit() {
    if (!form.media) {
        return $root.$emit('flash.error', 'Please add item image first.')
    }

    const route = isCreating.value
        ? $route('catalog.store')
        : $route('catalog.update', { item: item.value })

    const method = isCreating.value ? 'post' : 'patch'
    form.transform((data) => ({
        ...data,
        media: data.media.id,
        type: data.type,
    }))
    form[method](route, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            emit('refresh')
            close()
        },
        onError: (errors) => $root.$emit('flash.validation', errors),
    })
}

function useMedia(media) {
    form.media = media
}

function addNewItemType() {
    const searchText = searchType.value.trim()
    const options = itemTypes.value

    const itemNotFound = !options.find(
        (item) => item.text.toLowerCase() === searchText.toLowerCase(),
    )

    if (itemNotFound && searchText.length) {
        const newOption = {
            text: searchText,
            value: `new-${options.length + 1}`,
        }

        itemTypes.value = [...options, newOption]

        nextTick(() => {
            form.type = newOption.text
        })
    }
}

function normalizeType(val) {
    if (!val) return

    if (typeof val === 'object' && val.text) {
        form.type = val.text
        return
    }

    form.type = val
}
</script>

<template>
    <!-- eslint-disable vue/no-v-html -->

    <v-dialog
        v-model="isShow"
        persistent
        lazy
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{
                        isCreating
                            ? $t('catalog.labels.add-item')
                            : $t('catalog.labels.update-item')
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
                    <v-layout
                        row
                        wrap
                    >
                        <v-flex
                            xs12
                            sm6
                            class="tw-flex tw-flex-col tw-gap-y-3"
                        >
                            <v-text-field
                                v-model="form.name"
                                color="primary"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.name"
                                :label="$t('catalog.labels.item.item-name')"
                                outline
                                :hide-details="!form.errors.name"
                                required
                                class="required-input"
                            />
                            <v-combobox
                                v-model="form.type"
                                :items="itemTypes"
                                item-text="text"
                                item-value="text"
                                :search-input.sync="searchType"
                                outline
                                full-width
                                append-icon="none"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.type"
                                :label="$t('catalog.labels.item.type')"
                                class="required-input"
                                required
                                :hide-no-data="!searchType"
                                :menu-props="{ closeOnContentClick: true }"
                                @change="normalizeType"
                            >
                                <template #no-data>
                                    <v-list-tile @click="addNewItemType">
                                        <v-list-tile-content>
                                            <v-list-tile-title>
                                                <v-icon color="primary">
                                                    add
                                                </v-icon>
                                                {{
                                                    $t('buttons.create-new-one')
                                                }}
                                            </v-list-tile-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                </template>
                            </v-combobox>
                        </v-flex>
                        <v-flex
                            xs12
                            sm6
                        >
                            <JumboUploadButton
                                v-show="!form.media"
                                title="Add Image"
                                :container-size="{ xs12: true }"
                                @click:card="
                                    (e) => $refs.mediaAttachmentMenu.show(e)
                                "
                            >
                                <div class="v-messages error--text">
                                    {{ form.errors.media }}
                                </div>
                            </JumboUploadButton>

                            <v-card
                                v-if="form.media"
                                flat
                                tile
                                class="d-flex"
                                max-height="195"
                            >
                                <v-img :src="form.media.url">
                                    <v-layout
                                        align-start
                                        justify-end
                                    >
                                        <v-btn
                                            color="error"
                                            flat
                                            icon
                                            @click="form.media = null"
                                        >
                                            <v-icon> cancel </v-icon>
                                        </v-btn>
                                    </v-layout>
                                </v-img>
                            </v-card>
                        </v-flex>
                        <v-flex
                            xs12
                            sm6
                        >
                            <v-textarea
                                v-model="form.description"
                                :loading="form.processing"
                                :label="$t('catalog.labels.item.description')"
                                :error-messages="form.errors.description"
                                :hide-details="!form.errors.description"
                                outline
                                class="required-input"
                            />
                        </v-flex>
                        <v-flex
                            sm6
                            xs12
                        >
                            <v-layout
                                row
                                wrap
                            >
                                <v-flex sm6>
                                    <v-text-field
                                        v-model="form.manufacture_id"
                                        color="primary"
                                        outline
                                        :disabled="form.processing"
                                        :loading="form.processing"
                                        :error-messages="
                                            form.errors.manufacture_id
                                        "
                                        class="required-input"
                                        :label="
                                            $t(
                                                'catalog.labels.item.manufacture-id',
                                            )
                                        "
                                        :hide-details="
                                            !form.errors.manufacture_id
                                        "
                                    />
                                </v-flex>
                                <v-flex sm6>
                                    <v-select
                                        v-model="form.unit"
                                        item-text="text"
                                        item-value="value"
                                        outline
                                        full-width
                                        :items="unitOptions"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        :error-messages="form.errors.unit"
                                        :hide-details="!form.errors.unit"
                                        :label="$t('catalog.labels.item.unit')"
                                        class="required-input"
                                        required
                                    />
                                </v-flex>
                                <v-flex sm6>
                                    <v-text-field
                                        v-model="form.purchasing_price"
                                        color="primary"
                                        outline
                                        type="number"
                                        :disabled="form.processing"
                                        :loading="form.processing"
                                        :error-messages="
                                            form.errors.purchasing_price
                                        "
                                        :label="
                                            $t(
                                                'catalog.labels.item.purchasing-price',
                                            )
                                        "
                                        :hide-details="
                                            !form.errors.purchasing_price
                                        "
                                    >
                                        <template #prepend-inner>
                                            <div class="tw-font-semibold">
                                                {{ currency?.symbol }}
                                            </div>
                                        </template>
                                    </v-text-field>
                                </v-flex>

                                <v-flex sm6>
                                    <v-text-field
                                        v-model="form.selling_price"
                                        color="primary"
                                        outline
                                        type="number"
                                        class="required-input"
                                        :disabled="form.processing"
                                        :loading="form.processing"
                                        :error-messages="
                                            form.errors.selling_price
                                        "
                                        :label="
                                            $t(
                                                'catalog.labels.item.selling-price',
                                            )
                                        "
                                        :hide-details="
                                            !form.errors.selling_price
                                        "
                                    >
                                        <template #prepend-inner>
                                            <div class="tw-font-semibold">
                                                {{ currency?.symbol }}
                                            </div>
                                        </template>
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-divider />
            <v-card-actions class="px-4 py-4">
                <v-spacer />
                <v-btn
                    :disabled="form.processing"
                    outline
                    depressed
                    @click="close"
                >
                    {{ $t('buttons.cancel') }}
                </v-btn>
                <SubmitButton
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="submit"
                >
                    {{ $t(isCreating ? 'buttons.create' : 'buttons.update') }}
                    <template #loader>
                        <span> {{ loadingText }}... </span>
                    </template>
                </SubmitButton>
            </v-card-actions>
        </v-card>
        <FileAttachmentFormMenu
            ref="mediaAttachmentMenu"
            :allowed-types="['image/*']"
            @attach:from-file="(file) => useMedia(file)"
            @attach:from-media="() => $refs.mediaDrawerRef.show()"
        />
        <MediaDrawer
            ref="mediaDrawerRef"
            :allowed-types="['images']"
            right
            absolute
            width="320"
            style="z-index: 10"
            @attach="(media) => useMedia(media)"
        />
    </v-dialog>
</template>
