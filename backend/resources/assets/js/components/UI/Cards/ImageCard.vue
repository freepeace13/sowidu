<template>
    <v-card
        :color="color"
        class="cursor-clickable pa-1 d-flex"
        min-height="150px"
    >
        <v-img
            :lazy-src="photo"
            :src="photo"
            aspect-ratio="1.7778"
            v-bind="$attrs"
            class="position-relative"
        >
            <div :class="['progress', { 'd-none': progress === 100 || progress === 0 }]">
                <v-progress-circular
                    :rotate="360"
                    :size="100"
                    :width="15"
                    :value="progress"
                    color="white"
                />
            </div>
            <slot></slot>
        </v-img>
    </v-card>
</template>

<script>
export default {
    name: 'ImageCard',

    props: {
        url: {
            required: true,
            validator(v) {
                return typeof(v) === 'string' || v === null;
            },
        },

        progress: {
            type: Number,
            default: 0
        },

        highlighted: {
            type: Boolean,
            default: false
        },
    },

    computed: {
        color() {
            return ! this.highlighted ? 'white darken-1' : 'blue lighten-4';
        },

        photo() {
            return this.url || '/storage/placeholder.png';
        }
    }
}
</script>

<style scoped>
.progress {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>