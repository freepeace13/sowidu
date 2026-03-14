<template>
    <v-container grid-list-xs fluid class="image-slider">
        <p class="mb-0 text-xs-center" v-if="!images.length">No Attachment(s)</p>
        <v-layout row wrap v-else>
            <v-flex xs3 v-for="image in images" :key="image.id">
                <div class="image-slider-item">
                    <v-btn color="red" fab absolute small @click="$emit('remove', image)" class="image-remove-btn">
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-img :src="image.url" height="150px"/>
                </div>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    export default {
        props: {
            images: {
                type: Array,
                default: () => ([])
            }
        },
    }
</script>

<style lang="scss" scoped>
    .image-slider {
        border: 1px solid #616161;
        margin-bottom: 15px;

        > p {
            color: #616161;
        }

        .image-slider-item {
            border: 2px solid #ccc;
            position: relative;

            .image-remove-btn {
                right: -15px;
                top: -10px;
            }
        }
    }
</style>
