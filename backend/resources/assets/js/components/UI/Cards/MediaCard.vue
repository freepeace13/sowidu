<template>
    <DisplayCard
        v-bind="$attrs"
        :title="title"
        :photos="[photo]"
        @click:photo="$emit('click:photo')"
        @click:title="$emit('click:title')"
    >
        <template slot="subtitle">
            <span class="grey--text text-lighten-5">
                {{ description }}
                &middot;
                <v-icon v-html="video ? 'videocam' : 'image'" small></v-icon>
            </span>
        </template>

        <template slot="menu" v-if="!hideMenu">
            <slot name="menu" v-if="$scopedSlots.menu"></slot>
            <v-list light v-else>
                <v-list-tile>
                    <v-list-tile-content @click="$emit('menu:preview')">
                        {{ video ? 'Play Video' : 'Preview' }}
                    </v-list-tile-content>
                </v-list-tile>
                <v-list-tile v-if="!video && !isAvatar">
                    <v-list-tile-content @click="$emit('menu:avatar')">
                        Set as Avatar
                    </v-list-tile-content>
                </v-list-tile>
                <v-list-tile>
                    <v-list-tile-content @click="$emit('menu:edit')">
                        Edit Description
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </template>

        <slot></slot>
    </DisplayCard>
</template>

<script>
import DisplayCard from '~/components/layouts/DisplayCard';
import { mapState } from 'vuex';

export default {
    name: 'MediaCard',

    components: {
        DisplayCard
    },

    props: {
        title: {
            type: String,
            default: null
        },

        description: {
            type: String,
            default: null
        },

        type: {
            required: true,
            validator(v) {
                return true;
                // TODO: Refactor that should be usable in stories
                // return this.types.videos.indexOf(v) !== -1
                //     || this.types.images.indexOf(v) !== -1
                //     || v === undefined;
            }
        },

        src: {
            required: true,
            validator(v) {
                return typeof(v) === 'string' || v === undefined;
            }
        },

        video: {
            type: Boolean,
            default: true
        },

        isAvatar: {
            type: Boolean,
            default: false
        },

        hideMenu: {
            type: Boolean,
            default: false
        }
    },

    computed: {
        photo() {
            return this.video ? '/storage/video-placeholder.png' : this.src;
        },

        ...mapState({
            types: state => {
                console.log(state)
                return state.media.types
            }
        })
    }
}
</script>