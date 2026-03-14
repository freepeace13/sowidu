<template>
    <v-dialog
        v-model="isSharingFile"
        max-width="650"
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>{{
                    $t('headings.share-with-people')
                }}</v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="isSharingFile = false"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>

            <v-card-text>
                <v-autocomplete
                    ref="input"
                    :items="suggestions"
                    :search-input.sync="search"
                    :loading="isSearching"
                    :disabled="!policies.can_modify_members"
                    :placeholder="$t('hints.add-people')"
                    box
                    no-filter
                    hide-details
                    single-line
                    hide-no-data
                    @input="addMember"
                >
                    <template #item="{ item }">
                        <v-list-tile-avatar>
                            <img :src="item.photo" />
                        </v-list-tile-avatar>
                        <v-list-tile-content>
                            <v-list-tile-title>{{
                                item.name
                            }}</v-list-tile-title>
                            <v-list-tile-sub-title>{{
                                item.email
                            }}</v-list-tile-sub-title>
                        </v-list-tile-content>
                    </template>
                </v-autocomplete>

                <!-- <v-autocomplete
          v-model="form.members"
          :items="people"
          single-line
          hide-details
          placeholder="Add people and groups"
          multiple box
        /> -->
            </v-card-text>

            <v-list two-line>
                <v-list-tile>
                    <v-list-tile-avatar>
                        <img :src="target.owner.photo" />
                    </v-list-tile-avatar>

                    <v-list-tile-content>
                        <v-list-tile-title>
                            {{ target.owner.name }}
                        </v-list-tile-title>
                        <v-list-tile-sub-title>
                            {{ target.owner.email }}
                        </v-list-tile-sub-title>
                    </v-list-tile-content>

                    <v-list-tile-action>
                        {{ $t('labels.owner') }}
                    </v-list-tile-action>
                </v-list-tile>

                <FileMember
                    v-for="member in target.members"
                    :key="member.id"
                    :member="member"
                    :permission-types="permissionTypes"
                    :policies="policies"
                    @click:permission-menu="
                        ({ e, member }) =>
                            $refs.shareFilePermissionMenu.show(e, member)
                    "
                />
            </v-list>

            <v-card-actions>
                <v-spacer />

                <v-btn
                    color="primary"
                    @click="isSharingFile = false"
                >
                    {{ $t('buttons.done') }}
                </v-btn>
            </v-card-actions>
        </v-card>
        <ShareFilePermissionMenu
            ref="shareFilePermissionMenu"
            :permission-types="permissionTypes"
            :policies="policies"
            @click:remove="(member) => removeMember(member)"
            @click:modify-permission="
                ({ member, permission }) =>
                    modifyMemberPermission(member, permission)
            "
        />
    </v-dialog>
</template>

<script>
import FileMember from '@/Features/Media/Components/FileMember.vue'
import Http from '@/Modules/Http'
import ShareFilePermissionMenu from '@/Pages/Media/Partials/ShareFilePermissionMenu.vue'
import axios from 'axios'
import { debounce } from 'lodash'

export default {
    components: { ShareFilePermissionMenu, FileMember },

    props: {
        permissionTypes: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            isSharingFile: false,
            suggestions: [],
            search: null,
            isSearching: false,

            target: {
                members: [],
                owner: {
                    name: null,
                    photo: null,
                    email: null,
                },
            },

            policies: {
                can_modify_members: false,
                can_modify_permission: false,
            },
        }
    },

    watch: {
        search(value) {
            if (typeof value == 'object') return

            this.fetchSuggestions({
                media: this.target.uuid,
                _query: { search: value },
            })
        },
    },

    created() {
        this.fetchSuggestions = debounce((params) => {
            this.suggestions = []
            this.isSearching = true

            axios
                .get(this.$route('json.media.share.suggestions', params))
                .then((response) => {
                    console.log(response.data)
                    this.suggestions = response.data.suggestions
                })
                .catch(console.error)
                .finally(() => {
                    this.isSearching = false
                })
        }, 500)
    },

    methods: {
        fetchSettings(uuid) {
            this.suggestions = []

            return Http.get(
                this.$route('json.media.share.settings', { media: uuid }),
            )
                .then((response) => {
                    this.target = response.data.media
                    this.policies = response.data.policies
                })
                .catch(console.error)
                .finally(() => (this.isSharingFile = true))
        },

        start(target) {
            this.isSharingFile = false
            this.fetchSettings(target.uuid)
        },

        addMember(value) {
            if (typeof value == 'undefined') return

            this.$inertia.post(
                this.$route('media.share.store', { media: this.target.uuid }),
                {
                    member_id: value.id,
                    member_type: value.user_type,
                },
                {
                    preserveState: true,
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                    onSuccess: () => this.fetchSettings(this.target.uuid),
                },
            )

            this.$refs.input.setValue(undefined)
        },

        modifyMemberPermission(member, permission) {
            this.$inertia.put(
                this.$route('media.share.update', { media: this.target.uuid }),
                {
                    member_id: member.id,
                    member_type: member.user_type,
                    member_permission: permission,
                },
                {
                    preserveState: true,
                    onError: (errors) => {
                        this.$root.$emit('flash.validation', errors)
                    },
                    onSuccess: () => this.fetchSettings(this.target.uuid),
                },
            )
        },

        removeMember(member) {
            this.$inertia.delete(
                this.$route('media.share.destroy', { media: this.target.uuid }),
                {
                    data: {
                        member_id: member.id,
                        member_type: member.user_type,
                    },
                    preserveState: true,
                    errorBag: 'removeMember',
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                    onSuccess: () => this.fetchSettings(this.target.uuid),
                },
            )
        },
    },
}
</script>
