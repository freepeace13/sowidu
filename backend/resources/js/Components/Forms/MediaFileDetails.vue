<template>
    <v-dialog max-width="500" scrollable persistent :value="show">
        <v-card>
            <v-toolbar color="transparent" flat>
                <v-toolbar-title class="title">
                    {{ $t('labels.details') }}
                </v-toolbar-title>
                <v-spacer />
                <v-btn icon @click="show = false">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>

            <v-card-text class="pt-0">
                <v-subheader class="font-weight-bold pl-0 pr-0">
                    {{ $t('headings.general-info') }}
                    <v-divider class="ml-3" inset />
                </v-subheader>

                <v-layout class="mt-3" align-center wrap>
                    <v-flex xs4 class="grey--text text-darken-5">
                        {{ $t('labels.inputs.name') }}
                    </v-flex>
                    <v-flex>{{ media.fileName || media.name }}</v-flex>
                </v-layout>

                <v-layout class="mt-3" align-center wrap>
                    <v-flex xs4 class="grey--text text-darken-5">
                        {{ $t('labels.inputs.type') }}
                    </v-flex>
                    <v-flex class="text-capitalize">{{ media.type }}</v-flex>
                </v-layout>

                <v-layout class="mt-3" align-center wrap>
                    <v-flex xs4 class="grey--text text-darken-5">
                        {{ $t('labels.inputs.size') }}
                    </v-flex>
                    <v-flex>{{ media.size }}</v-flex>
                </v-layout>

                <v-layout class="mt-3" align-center wrap>
                    <v-flex xs4 class="grey--text text-darken-5">
                        {{ $t('labels.inputs.location') }}
                    </v-flex>
                    <v-flex>
                        <a href="#" style="text-decoration: none">
                            <v-layout align-center>
                                <v-flex shrink>
                                    <v-icon left>folder</v-icon>
                                </v-flex>
                                <v-flex>{{ media.location }}</v-flex>
                            </v-layout>
                        </a>
                    </v-flex>
                </v-layout>

                <v-layout class="my-3" align-center wrap>
                    <v-flex xs4 class="grey--text text-darken-5">
                        {{ $t('labels.inputs.modified') }}
                    </v-flex>
                    <v-flex>{{ media.modified }}</v-flex>
                </v-layout>

                <v-subheader class="font-weight-bold pl-0 pr-0">
                    {{ $t('headings.sharing') }}
                    <v-divider class="ml-3" inset />
                </v-subheader>

                <v-list dense>
                    <v-list-tile
                        v-for="member in media.members"
                        :key="member.id"
                    >
                        <v-list-tile-avatar>
                            <v-img :src="member.photo" />
                        </v-list-tile-avatar>
                        <v-list-tile-content>
                            <v-list-tile-title
                                class="grey--text text-darken-5"
                                >{{ member.name }}</v-list-tile-title
                            >
                        </v-list-tile-content>
                        <v-list-tile-action v-if="member.isOwner">
                            {{ $t('labels.owner') }}
                        </v-list-tile-action>
                    </v-list-tile>
                </v-list>

                <v-subheader class="font-weight-bold pl-0 pr-0">
                    {{ $t('labels.category') }} <v-divider class="ml-3" inset />
                </v-subheader>

                <v-menu>
                    <template #activator="{ on }">
                        <v-list dense class="py-0">
                            <v-list-tile>
                                <v-list-tile-avatar>
                                    <v-icon>category</v-icon>
                                </v-list-tile-avatar>
                                <v-list-tile-content>
                                    <v-list-tile-title>
                                        <span
                                            v-if="media.category"
                                            class="text-capitalize"
                                            >{{ media.category }}</span
                                        >
                                        <span v-else>{{
                                            $t('hints.add-a-category')
                                        }}</span>
                                    </v-list-tile-title>
                                </v-list-tile-content>
                                <v-list-tile-action>
                                    <v-btn icon v-on="on">
                                        <v-icon>edit</v-icon>
                                    </v-btn>
                                </v-list-tile-action>
                            </v-list-tile>
                        </v-list>
                    </template>

                    <v-list dense>
                        <v-list-tile
                            v-for="[text, key] of Object.entries(categoryTypes)"
                            :key="`category-${key}`"
                            :class="{
                                'grey lighten-4': key === media.category,
                            }"
                            @click="selectCategory(key)"
                        >
                            <v-list-tile-content>{{
                                text
                            }}</v-list-tile-content>
                        </v-list-tile>
                    </v-list>
                </v-menu>

                <template v-if="!!additionalFields.length">
                    <v-subheader class="font-weight-bold pl-0 pr-0">
                        {{ $t('headings.custom-fields') }}
                        <v-divider class="ml-3" inset />
                    </v-subheader>

                    <div
                        v-for="field in additionalFields"
                        :key="`custom_field-${field.name}`"
                        class="mb-2"
                    >
                        <v-layout align-center wrap>
                            <v-flex xs4 class="grey--text text-darken-4">
                                {{ $t(`labels.inputs.${field.name}`) }}
                            </v-flex>

                            <v-flex class="grey lighten-5">
                                <v-text-field
                                    :ref="`field_${field.name}`"
                                    v-model="media.customFields[field.name]"
                                    full-width
                                    single-line
                                    hide-details
                                    :name="field.name"
                                    :mask="field.mask"
                                    :placeholder="field.placeholder"
                                    :readonly="field.name !== editingField"
                                    :append-icon="
                                        field.name == editingField
                                            ? 'save'
                                            : 'edit'
                                    "
                                    :return-masked-value="
                                        field.returnMaskedValue
                                    "
                                    @blur="saveCustomField(field.name)"
                                    @click:append="
                                        field.name == editingField
                                            ? saveCustomField(field.name)
                                            : activateEditField(field.name)
                                    "
                                />
                            </v-flex>
                        </v-layout>
                    </div>
                </template>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
import Http from '@/Modules/Http'
import { makeDetails, makeCustomField, getCustomFieldNames } from '../../utils'

export default {
    props: {
        categoryTypes: {
            type: Object,
            required: true,
        },
    },

    data: () => ({
        show: false,
        editingField: null,
        originalFieldValue: null,

        media: {
            id: null,
            uuid: null,
            name: null,
            fileName: null,
            type: null,
            size: null,
            location: null,
            modified: null,
            members: [],
            category: null,
            customFields: {},
        },
    }),

    computed: {
        icon() {
            return (
                {
                    image: 'image',
                    video: 'movie',
                    pdf: 'picture_as_pdf',
                    folder: 'folder',
                }[this.media.type] || null
            )
        },

        additionalFields() {
            return (
                {
                    invoice: getCustomFieldNames('invoice').map((fieldName) =>
                        makeCustomField({
                            name: fieldName,
                            value: this.media.customFields[fieldName] || null,
                        }),
                    ),

                    offer: [],
                    delivery: [],
                    order: [],
                }[this.media.category] || []
            )
        },
    },

    methods: {
        start(media) {
            Http.get(this.$route('media.show', { media: media.uuid }))
                .then((response) => {
                    this.resetMediaFields()
                    this.media = makeDetails(response.data.file)
                    this.show = true
                })
                .catch(console.error)
        },

        resetMediaFields() {
            this.media.uuid = null
            this.media.category = null
            this.media.customFields = {}
        },

        activateEditField(field) {
            this.editingField = field
            this.originalFieldValue = this.media.customFields[field]

            this.$nextTick(() => {
                this.$refs[`field_${field}`][0].$el
                    .querySelector('input')
                    .focus()
            })
        },

        saveCustomField(fieldName) {
            const payload = {
                name: fieldName,
                value: this.media.customFields[fieldName],
            }

            Http.put(
                this.$route('json.media.customField.update', {
                    media: this.media.uuid,
                }),
                payload,
            )
                .then((response) => {
                    this.resetMediaFields()
                    this.media = makeDetails(response.data.file)
                    this.editingField = null
                })
                .catch(console.error)
        },

        selectCategory(category) {
            Http.put(
                this.$route('json.media.category.update', {
                    media: this.media.uuid,
                }),
                { category },
            )
                .then((response) => {
                    this.media = makeDetails(response.data.file)
                })
                .catch(console.error)
        },
    },
}
</script>
