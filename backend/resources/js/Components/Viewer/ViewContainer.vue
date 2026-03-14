<template>
    <v-layout fill-height>
        <v-flex>
            <v-card
                color="transparent"
                class="fill-height"
                style="position: relative"
                dark
            >
                <v-card-text
                    class="fill-height faded-black"
                    style="padding-top: 76px"
                    :style="{ overflowY: scrollable ? 'auto' : 'hidden' }"
                >
                    <v-toolbar
                        class="gradient-black mr-2"
                        tile
                        absolute
                        flat
                        dark
                    >
                        <v-btn
                            icon
                            @click="close()"
                        >
                            <v-icon>keyboard_backspace</v-icon>
                        </v-btn>

                        <v-toolbar-title>
                            <v-icon color="red">{{ details.fileIcon }}</v-icon>
                            {{ details.fileName }}
                        </v-toolbar-title>

                        <v-spacer />

                        <v-btn
                            icon
                            @click="download()"
                        >
                            <v-icon>file_download</v-icon>
                        </v-btn>

                        <v-btn
                            icon
                            @click="share()"
                        >
                            <v-icon>share</v-icon>
                        </v-btn>

                        <v-btn
                            icon
                            @click="showDetails = !showDetails"
                        >
                            <v-icon>info</v-icon>
                        </v-btn>

                        <!-- <v-menu offset-y left z-index="999" min-width="250">
              <template #activator="{ on }">
                <v-btn icon v-on="on">
                  <v-icon>more_vert</v-icon>
                </v-btn>
              </template>

              <v-list>
                <v-list-tile @click="showDetails = true">
                  <v-list-tile-avatar>
                    <v-icon>info</v-icon>
                  </v-list-tile-avatar>
                  <v-list-tile-content>
                    Details
                  </v-list-tile-content>
                </v-list-tile>
              </v-list>
            </v-menu> -->
                    </v-toolbar>

                    <slot />
                </v-card-text>
            </v-card>
        </v-flex>

        <v-flex
            v-if="showDetails"
            shrink
        >
            <v-navigation-drawer
                width="400"
                dark
                right
                clipped
                :value="showDetails"
            >
                <v-card
                    color="grey darken-3"
                    class="fill-height hide-overflow"
                    tile
                >
                    <v-card-text
                        style="overflow-y: auto; margin-top: 64px"
                        class="fill-height"
                    >
                        <v-toolbar
                            color="grey darken-3"
                            absolute
                        >
                            <v-toolbar-title>Details</v-toolbar-title>
                            <v-spacer />
                            <v-btn
                                icon
                                @click="showDetails = !showDetails"
                            >
                                <v-icon>close</v-icon>
                            </v-btn>
                        </v-toolbar>

                        <!-- General Info -->
                        <div>
                            <v-subheader
                                class="pl-0 pr-0 white--text subheading font-weight-regular"
                            >
                                {{ $t('headings.general-info') }}
                                <v-divider
                                    class="ml-3"
                                    inset
                                />
                            </v-subheader>

                            <v-layout
                                align-center
                                wrap
                            >
                                <v-flex
                                    xs4
                                    class="grey--text text-darken-5"
                                >
                                    {{ $t('labels.inputs.type') }}
                                </v-flex>
                                <v-flex class="text-capitalize">{{
                                    details.type
                                }}</v-flex>
                            </v-layout>

                            <v-layout
                                class="mt-3"
                                align-center
                                wrap
                            >
                                <v-flex
                                    xs4
                                    class="grey--text text-darken-5"
                                >
                                    {{ $t('labels.inputs.size') }}
                                </v-flex>
                                <v-flex>{{ details.size }}</v-flex>
                            </v-layout>

                            <v-layout
                                class="mt-3"
                                align-center
                                wrap
                            >
                                <v-flex
                                    xs4
                                    class="grey--text text-darken-5"
                                >
                                    {{ $t('labels.inputs.location') }}
                                </v-flex>
                                <v-flex>
                                    <a
                                        href="#"
                                        style="text-decoration: none"
                                        class="white--text"
                                    >
                                        <v-layout align-center>
                                            <v-flex shrink>
                                                <v-icon left>folder</v-icon>
                                            </v-flex>
                                            <v-flex>{{
                                                details.location
                                            }}</v-flex>
                                        </v-layout>
                                    </a>
                                </v-flex>
                            </v-layout>

                            <v-layout
                                class="my-3"
                                align-center
                                wrap
                            >
                                <v-flex
                                    xs4
                                    class="grey--text text-darken-5"
                                >
                                    {{ $t('labels.inputs.modified') }}
                                </v-flex>
                                <v-flex>{{ details.modified }}</v-flex>
                            </v-layout>

                            <v-layout
                                class="my-3"
                                align-center
                                wrap
                            >
                                <v-flex
                                    xs4
                                    class="grey--text text-darken-5"
                                >
                                    {{ $t('labels.modified_by') }}
                                </v-flex>
                                <v-flex>{{ details.modified }}</v-flex>
                            </v-layout>
                        </div>

                        <!-- Sharing -->
                        <div>
                            <v-subheader
                                class="pl-0 pr-0 white--text subheading font-weight-regular"
                            >
                                {{ $t('headings.sharing') }}
                                <v-divider
                                    class="ml-3"
                                    inset
                                />
                            </v-subheader>

                            <v-list>
                                <v-list-tile
                                    v-for="member in details.members"
                                    :key="member.id"
                                >
                                    <v-list-tile-avatar>
                                        <v-img :src="member.photo" />
                                    </v-list-tile-avatar>
                                    <v-list-tile-content>
                                        <v-list-tile-title
                                            class="grey--text text-darken-5"
                                            >{{
                                                member.name
                                            }}</v-list-tile-title
                                        >
                                    </v-list-tile-content>
                                    <v-list-tile-action v-if="member.isOwner">
                                        {{ $t('labels.owner') }}
                                    </v-list-tile-action>
                                </v-list-tile>
                            </v-list>
                        </div>

                        <!-- Category -->
                        <div>
                            <v-subheader
                                class="pl-0 pr-0 white--text subheading font-weight-regular"
                            >
                                {{ $t('labels.category') }}
                                <v-divider
                                    class="ml-3"
                                    inset
                                />
                            </v-subheader>

                            <v-menu z-index="999">
                                <template #activator="{ on }">
                                    <v-list class="py-0">
                                        <v-list-tile>
                                            <v-list-tile-avatar>
                                                <v-icon>category</v-icon>
                                            </v-list-tile-avatar>

                                            <v-list-tile-content>
                                                <v-list-tile-title>
                                                    <span
                                                        v-if="details.category"
                                                        class="text-capitalize"
                                                        >{{
                                                            details.category
                                                        }}</span
                                                    >
                                                    <span v-else>{{
                                                        $t(
                                                            'hints.add-a-category',
                                                        )
                                                    }}</span>
                                                </v-list-tile-title>
                                            </v-list-tile-content>

                                            <v-list-tile-action>
                                                <v-btn
                                                    icon
                                                    v-on="on"
                                                >
                                                    <v-icon>edit</v-icon>
                                                </v-btn>
                                            </v-list-tile-action>
                                        </v-list-tile>
                                    </v-list>
                                </template>

                                <v-list>
                                    <v-list-tile
                                        v-for="[text, key] of Object.entries(
                                            $page.props.categoryTypes,
                                        )"
                                        :key="`category-${key}`"
                                        :class="{
                                            'grey lighten-4':
                                                key === details.category,
                                        }"
                                        @click="selectCategory(key)"
                                    >
                                        <v-list-tile-content>{{
                                            text
                                        }}</v-list-tile-content>
                                    </v-list-tile>
                                </v-list>
                            </v-menu>
                        </div>

                        <!-- Custom fields -->
                        <div v-if="!!additionalFields.length">
                            <v-subheader
                                class="pl-0 pr-0 white--text subheading font-weight-regular"
                            >
                                {{ $t('headings.custom-fields') }}

                                <v-divider
                                    class="ml-3"
                                    inset
                                />

                                <template
                                    v-if="
                                        details.customFields.modifiedBy &&
                                        details.customFields.modifiedLast
                                    "
                                >
                                    <v-menu
                                        z-index="999"
                                        offset-y
                                        offset-x
                                    >
                                        <template #activator="{ on }">
                                            <v-btn
                                                icon
                                                small
                                                class="my-0 mr-0"
                                                v-on="on"
                                            >
                                                <v-icon>watch_later</v-icon>
                                            </v-btn>
                                        </template>

                                        <v-card>
                                            <v-card-text class="py-1 px-2">
                                                {{
                                                    $t('messages.edited_info', {
                                                        name: details
                                                            .customFields
                                                            .modifiedBy,
                                                        date: details
                                                            .customFields
                                                            .modifiedLast,
                                                    })
                                                }}
                                            </v-card-text>
                                        </v-card>
                                    </v-menu>
                                </template>
                            </v-subheader>

                            <div
                                v-for="field in additionalFields"
                                :key="`custom_field-${field.name}`"
                                class="mb-2"
                            >
                                <v-layout
                                    align-center
                                    wrap
                                >
                                    <v-flex
                                        xs4
                                        class="grey--text text-darken-4"
                                    >
                                        {{ $t(`labels.inputs.${field.name}`) }}
                                    </v-flex>

                                    <v-flex>
                                        <v-text-field
                                            :ref="`field_${field.name}`"
                                            v-model="
                                                details.customFields[field.name]
                                            "
                                            solo
                                            solo-inverted
                                            flat
                                            hide-details
                                            :name="field.name"
                                            :mask="field.mask"
                                            :placeholder="field.placeholder"
                                            :readonly="
                                                field.name !== editingField
                                            "
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
                                                    ? saveCustomField(
                                                          field.name,
                                                      )
                                                    : activateEditField(
                                                          field.name,
                                                      )
                                            "
                                        />
                                    </v-flex>
                                </v-layout>
                            </div>
                        </div>
                    </v-card-text>
                </v-card>
            </v-navigation-drawer>
        </v-flex>
    </v-layout>
</template>

<script>
import Http from '@/Modules/Http'
import { makeDetails, getCustomFieldNames, makeCustomField } from '../../utils'

export default {
    inject: ['close', 'download', 'share', 'media', 'reset'],

    props: {
        scrollable: {
            type: Boolean,
            default: false,
        },
    },

    data: () => ({
        showDetails: false,
        editingField: null,
        originalFieldValue: null,
    }),

    computed: {
        details() {
            return makeDetails(this.media())
        },

        additionalFields() {
            return (
                {
                    invoice: getCustomFieldNames('invoice').map((fieldName) =>
                        makeCustomField({
                            name: fieldName,
                            value: this.details.customFields[fieldName] || null,
                        }),
                    ),

                    offer: [],
                    delivery: [],
                    order: [],
                }[this.details.category] || []
            )
        },
    },

    methods: {
        activateEditField(field) {
            this.editingField = field
            this.originalFieldValue = this.details.customFields[field]

            this.$nextTick(() => {
                this.$refs[`field_${field}`][0].$el
                    .querySelector('input')
                    .focus()
            })
        },

        saveCustomField(fieldName) {
            const payload = {
                name: fieldName,
                value: this.details.customFields[fieldName],
            }

            Http.put(
                this.$route('json.media.customField.update', {
                    media: this.details.uuid,
                }),
                payload,
            )
                .then((response) => {
                    this.reset(response.data.file)
                    this.editingField = null
                })
                .catch(console.error)
        },

        selectCategory(category) {
            Http.put(
                this.$route('json.media.category.update', {
                    media: this.details.uuid,
                }),
                { category },
            )
                .then((response) => {
                    this.reset(response.data.file)
                })
                .catch(console.error)
        },
    },
}
</script>

<style lang="scss" scoped>
.gradient-black {
    background: rgb(0, 0, 0);
    background: linear-gradient(
        180deg,
        rgba(0, 0, 0, 0.6306897759103641) 26%,
        rgba(255, 255, 255, 0) 100%
    );
}

.faded-black {
    background: rgba(0, 0, 0, 0.9);
}
</style>
