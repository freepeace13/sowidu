<template>
    <v-card-text>
        <v-container
            fluid
            pa-0
        >
            <v-layout
                row
                class="!tw-flex-wrap tw-items-start tw-justify-start -tw-mx-2 tw-flex-grow-1"
            >
                <v-checkbox
                    v-model="all"
                    single-line
                    height="30"
                    color="primary"
                    class="tw-mt-0 tw-mb-0 tw-px-2"
                    hide-details
                >
                    <template #label>
                        <span class="tw-font-bold tw-text-black">
                            {{ $t('labels.all') }}
                        </span>
                    </template>
                </v-checkbox>
                <v-checkbox
                    v-for="category in categories"
                    :key="`filter-category-${category}`"
                    v-model="selected"
                    single-line
                    multiple
                    height="30"
                    color="primary"
                    hide-details
                    :value="category"
                    class="tw-mt-0 tw-mb-0 tw-px-2"
                >
                    <template #label>
                        <div class="tw-ml-1 tw-capitalize">{{ category }}</div>
                    </template>
                </v-checkbox>
                <v-checkbox
                    v-model="selected"
                    single-line
                    multiple
                    hide-details
                    height="30"
                    color="primary"
                    value="no-category"
                    class="tw-mt-0 tw-mb-0 tw-px-2"
                >
                    <template #label>
                        <div
                            class="tw-ml-1 tw-capitalize tw-italic tw-font-light"
                        >
                            {{ $t('media.labels.no-category') }}
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
        categories: {
            required: true,
            type: Array,
        },
    },

    data: () => ({
        selected: [],
    }),

    computed: {
        all: {
            get: function () {
                return !this.selected.length
            },
            set: function (val) {
                if (val) this.selected = []
            },
        },
    },

    watch: {
        selected(filterCategories) {
            this.$emit('input', filterCategories)
        },
    },
}
</script>
