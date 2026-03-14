<template>
    <v-card
        class="tw-animate-pulse"
        style="position: relative"
    >
        <div
            class="tw-flex tw-items-center tw-justify-center tw-border-b border-solid-b tw-border-gray-300"
            style="height: 232px"
        >
            <SkeletonAvatar
                tile
                class="tw-w-full tw-opacity-50"
                :icon="typeIcon"
                :size="116"
                :icon-color="iconColor"
            />
        </div>

        <v-list class="py-0">
            <v-list-tile pl-0>
                <v-list-tile-avatar min-w-auto>
                    <v-icon :color="iconColor">
                        {{ typeIcon }}
                    </v-icon>
                </v-list-tile-avatar>

                <v-list-tile-content>
                    <v-list-tile-title class="tw-flex tw-items-center">
                        <SkeletonBar />
                    </v-list-tile-title>
                </v-list-tile-content>

                <v-list-tile-action>
                    <SkeletonBar class="tw-w-4" />
                </v-list-tile-action>
            </v-list-tile>
        </v-list>
    </v-card>
</template>
<script>
import SkeletonAvatar from '@components/Skeleton/SkeletonAvatar.vue'
import SkeletonBar from '@components/Skeleton/SkeletonBar.vue'
export default {
    components: {
        SkeletonAvatar,
        SkeletonBar,
    },

    props: {
        type: {
            type: Array,
            required: false,
            default: () => [],
        },
    },

    computed: {
        icon() {
            if (!this.type.length) {
                return {
                    color: 'red',
                    name: 'perm_media',
                }
            }

            let type = this.type
            if (Array.isArray(type)) {
                type = type[Math.floor(Math.random() * type.length)]
            }

            return {
                documents: { name: 'picture_as_pdf', color: 'green' },
                images: { name: 'image', color: 'red' },
                videos: { name: 'movie', color: 'blue' },
            }[type]
        },
        typeIcon() {
            return this.icon.name
        },
        iconColor() {
            return this.icon.color
        },
    },
}
</script>
<style lang="css" scoped>
.border-solid-b {
    border-bottom-style: solid;
}
</style>
