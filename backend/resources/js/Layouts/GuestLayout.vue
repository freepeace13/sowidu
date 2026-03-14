<template>
    <v-app>
        <!-- <app-toolbar
            v-if="$page.props.auth.authenticated"
            :title="$page.props.title"
        >
            <template v-if="$scopedSlots.extension !== undefined" #extension>
                <slot name="extension" />
            </template>

            <template v-if="$scopedSlots.links !== undefined" #links>
                <slot name="links" />
            </template>
        </app-toolbar> -->

        <v-content>
            <v-snackbar
                :value="$page.props?.flash?.type"
                :color="flash?.color"
                :timeout="15000"
                bottom
            >
                <v-icon large>{{ flash?.icon }}</v-icon>

                <span class="shrink">
                    {{ $page.props?.flash?.message }}
                </span>
            </v-snackbar>

            <v-container
                fluid
                grid-list-lg
                fill-height
            >
                <!-- Default Slot -->
                <slot />
            </v-container>
        </v-content>

        <portal-target
            name="right-drawers"
            multiple
        />
        <HeadTitle :title="title" />
    </v-app>
</template>

<script>
import { Head as HeadTitle } from '@inertiajs/vue2'

export default {
    components: { HeadTitle },

    props: {
        title: {
            required: false,
            type: String,
            default: 'Sowidu',
        },
    },

    computed: {
        flash() {
            const flash = this.$page.props?.flash

            if (flash?.type) {
                return {
                    success: {
                        color: 'green darken-1',
                        icon: 'check_circle',
                    },
                    error: {
                        color: 'red darken-1',
                        icon: 'error_outline',
                    },
                }[flash.type]
            }

            return {}
        },
    },
}
</script>
