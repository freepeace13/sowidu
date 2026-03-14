<template>
    <v-card v-bind="$attrs" flat tile class="mb-2">
        <v-card-text>
            <v-layout row>
                <v-flex shrink align-self-center>
                    <v-avatar size="36px">
                        <v-img :lazy-src="avatar" :src="avatar" />
                    </v-avatar>
                </v-flex>
                <v-flex align-self-center class="px-3">
                    <div>
                        <span class="body-2 font-weight-bold">
                            {{ title }}
                        </span>

                        <template v-if="subtitle">
                            &middot;
                            <span class="caption">{{ subtitle }}</span>
                        </template>
                    </div>

                    <template v-if="caption">
                        <span class="caption">{{ caption }}</span>
                        &middot;
                    </template>

                    <span class="caption">{{ postedOn }}</span>
                </v-flex>
            </v-layout>

            <v-layout row>
                <v-flex xs10 offset-xs1 class="py-3 body-1">
                    <slot></slot>
                </v-flex>
            </v-layout>
        </v-card-text>
    </v-card>
</template>

<script>
export default {
    name: 'CommentTile',

    props: {
        avatar: {
            type: String,
            required: true,
        },

        title: {
            type: String,
            required: true,
        },

        subtitle: {
            type: String
        },

        caption: {
            type: String
        },

        postedOn: {
            type: String,
            required: true,
        }
    }
}
</script>