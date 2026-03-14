<template>
    <v-card>
        <div class="d-flex">
            <v-layout row>
                <v-flex xs4 class="media pr-0">
                    <v-icon large class="icon">event_note</v-icon>
                </v-flex>
                <v-flex xs8 class="pl-0">
                    <v-card-text class="pa-3">
                        <div class="d-flex align-items-center">
                            <div class="flex xs9 pa-0 headline cursor-clickable">
                                {{ value.name }}
                            </div>
                            <!-- <div :class="['label flex xs3 pa-0', 'enabled']">
                                Enabled
                            </div> -->
                        </div>
                        <p class="mb-0">{{ value.description }}</p>
                    </v-card-text>

                    <div class="details">
                        <v-card-text class="px-3 py-2">
                            Items <span class="float-right">{{ value.items.length }}</span>
                        </v-card-text>
                    </div>
                </v-flex>
            </v-layout>
        </div>

        <v-divider></v-divider>

        <v-card-actions class="d-block">
            <v-btn flat block @click="viewCatalogueForm">
                <v-icon size="15" class="mr-1">edit</v-icon> Edit Details
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
    export default {
        props: ['value'],

        methods: {
            viewCatalogueForm() {
                this.$modal.show({
                    modal: require('~/components/UI/Modals/Catalogue/CatalogueForm').default,
                    size: 'md',
                    title: this.value.name,
                    attrs: {
                        catalogue: this.value
                    },
                    listeners: {
                        success: (catalogue) => {
                            this.$store.dispatch('catalogue/fetchCatalogues')
                        }
                    }
                })
            }
        }
    }
</script>

<style lang="scss" scoped>
    .media {
        position: relative;
        .creator-avatar {
            position: absolute;
            bottom: 15px;
            left: 15px;
        }
    }

    .label {
        text-align: center;
        font-size: 12px;
        line-height: 30px;

        &.enabled {
            background: green;
        }

        &.disabled {
            background: grey;
        }
    }

    .icon {
        height: 200px;
        line-height: 200px;
        text-align: center;
        background: #303030;
        font-size: 10rem !important;
        display: block;
        color: #616161;
    }

    .details {
        min-height: 115px;
        max-height: 115px;
        overflow-y: auto;
    }
</style>
