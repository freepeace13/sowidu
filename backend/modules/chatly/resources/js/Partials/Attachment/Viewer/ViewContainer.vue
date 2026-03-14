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
                            {{ file?.name }}
                        </v-toolbar-title>
                        <v-spacer />
                        <v-btn
                            icon
                            @click="download()"
                        >
                            <v-icon>file_download</v-icon>
                        </v-btn>
                    </v-toolbar>
                    <slot />
                </v-card-text>
            </v-card>
        </v-flex>
    </v-layout>
</template>
<script>
export default {
    inject: ['attachment', 'close', 'download'],

    props: {
        scrollable: {
            type: Boolean,
            default: false,
        },
    },

    computed: {
        file() {
            return this.attachment()
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
