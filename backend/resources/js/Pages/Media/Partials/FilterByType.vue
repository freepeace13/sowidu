<template>
    <v-card-text>
        <v-container
            fluid
            pa-0
        >
            <v-layout row>
                <v-checkbox
                    v-model="all"
                    single-line
                    height="30"
                    color="primary"
                >
                    <template #label>
                        <span class="tw-font-bold tw-text-black">{{
                            $t('media.categories.any')
                        }}</span>
                    </template>
                </v-checkbox>
                <v-divider
                    dark
                    inset
                    vertical
                />
                <v-checkbox
                    v-for="(mediaType, idx) in types"
                    :key="`filter-type-${idx}`"
                    v-model="type"
                    :color="mediaType.color"
                    single-line
                    multiple
                    height="30"
                    :value="idx"
                >
                    <template #label>
                        <v-icon :color="mediaType.color">
                            {{ mediaType.icon }}
                        </v-icon>
                        <div class="tw-ml-1 tw-capitalize">
                            {{ $t(`labels.${idx}`) }}
                        </div>
                    </template>
                </v-checkbox>
            </v-layout>
        </v-container>
    </v-card-text>
</template>

<script>
export default {
    props: {
        value: {
            type: Array,
            required: true,
        },
    },

    data: () => ({
        type: [],
    }),

    computed: {
        all: {
            get: function () {
                return !this.type.length
            },
            set: function (val) {
                if (val) this.type = []
            },
        },
        types() {
            return {
                images: {
                    icon: 'image',
                    color: 'red',
                },
                videos: {
                    icon: 'video_library',
                    color: 'blue',
                },
                documents: {
                    icon: 'description',
                    color: 'green',
                },
            }
        },
    },

    watch: {
        type(filterTypes) {
            this.$emit('input', filterTypes)
        },
    },
}
</script>
