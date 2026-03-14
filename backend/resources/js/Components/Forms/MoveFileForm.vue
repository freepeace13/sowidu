<template>
    <v-menu
        v-model="isMovingFile"
        absolute
        offset-y
        bottom
        full-width
        :attach="attach"
        :close-on-content-click="false"
        min-width="430"
        lazy
        z-index="1"
    >
        <v-card @dblclick.native.stop.prevent="() => {}">
            <v-toolbar flat>
                <v-btn
                    v-if="showBackBtn"
                    icon
                    @click.stop="navigateTo(currentFolder.folder.uuid)"
                >
                    <v-icon>keyboard_backspace</v-icon>
                </v-btn>

                <v-toolbar-title class="subheading">
                    {{ currentFolder.exists ? currentFolder.name : 'My Drive' }}
                </v-toolbar-title>

                <v-spacer />

                <v-btn icon @click.stop="close">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>

            <v-divider />

            <v-card-text
                class="fill-height pa-0"
                style="min-height: 200px; max-height: 200px; overflow-y: auto"
            >
                <v-list>
                    <template v-for="folder in currentFolder.folders">
                        <v-list-tile
                            :key="folder.uuid"
                            :value="form.to == folder.uuid"
                            :dark="form.to == folder.uuid"
                            :disabled="target.uuid === folder.uuid"
                            active-class="primary white--text"
                            @click.stop="setDestination(folder.uuid)"
                            @dblclick.native="navigateTo(folder.uuid)"
                        >
                            <v-list-tile-avatar>
                                <v-icon>folder</v-icon>
                            </v-list-tile-avatar>
                            <v-list-tile-content>
                                <v-list-tile-title>{{
                                    folder.name
                                }}</v-list-tile-title>
                            </v-list-tile-content>

                            <v-list-tile-action>
                                <v-icon @click.stop="navigateTo(folder.uuid)">
                                    chevron_right
                                </v-icon>
                            </v-list-tile-action>
                        </v-list-tile>
                    </template>
                </v-list>
            </v-card-text>

            <v-card-actions>
                <v-spacer />

                <v-btn
                    color="primary"
                    :disabled="!enableMoveBtn"
                    @click="onMove"
                >
                    {{
                        form.to === currentFolder.uuid
                            ? $t('buttons.move-here')
                            : $t('buttons.move')
                    }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-menu>
</template>

<script>
import Http from '@/Modules/Http'

export default {
    props: {
        prefix: {
            type: String,
            default: 'media',
        },
    },

    data: (vm) => ({
        isMovingFile: false,

        form: vm.$inertia.form({
            to: null,
        }),

        target: {
            id: null,
            uuid: null,
            exists: false,
            folder: {
                id: null,
                uuid: null,
                name: null,
                exists: false,
            },
        },

        currentFolder: {
            id: null,
            uuid: null,
            name: null,
            exists: false,
            policies: {
                can_move_here: false,
            },
            folder: {
                id: null,
                uuid: null,
                name: null,
            },
            folders: [],
        },
    }),

    computed: {
        enableMoveBtn() {
            return (
                this.target.folder.uuid !== this.form.to &&
                this.currentFolder.policies.can_move_here
            )
        },

        showBackBtn() {
            return (
                this.currentFolder.folder.uuid !== null ||
                this.currentFolder.uuid !== null
            )
        },

        attach() {
            return this.target
                ? `#${this.prefix}_${this.target.is_dir ? 'folder' : 'file'}_${
                      this.target.id
                  }`
                : null
        },
    },

    methods: {
        onMove() {
            this.form.put(
                this.$route('media.move', { media: this.target.uuid }),
                {
                    errorBag: 'moveMedia',
                    preserveScroll: true,
                    onSuccess: () => this.$emit('success'),
                },
            )
        },

        start(target) {
            this.target = target

            this.$nextTick(() => {
                this.navigateTo(this.target.folder.uuid).then(() => {
                    this.isMovingFile = true
                })
            })
        },

        close() {
            this.form.to = null
            this.isMovingFile = false

            this.target = {
                id: null,
                uuid: null,
                exists: false,
                folder: {
                    id: null,
                    uuid: null,
                    name: null,
                    exists: false,
                },
            }

            this.currentFolder = {
                id: null,
                uuid: null,
                name: null,
                exists: false,
                folder: {
                    id: null,
                    uuid: null,
                    name: null,
                },
                folders: [],
            }
        },

        setDestination(folder) {
            this.form.to =
                this.form.to !== folder ? folder : this.currentFolder.uuid
        },

        navigateTo(folder = null) {
            let route = this.$route('media.folders.show', {
                folder,
                _query: {
                    target: this.target.uuid,
                },
            })

            return Http.get(route)
                .then((response) => {
                    this.currentFolder = response.data.folder
                    this.form.to = response.data.folder.uuid

                    return response.data.folder
                })
                .catch(console.error)
        },
    },
}
</script>
