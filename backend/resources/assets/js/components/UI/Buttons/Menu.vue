<template>
    <div class="button-menu d-inline-block">
        <v-menu offset-y>
            <template v-slot:activator="{ on }">
                <v-btn
                    color="primary"
                    dark
                    v-on="on"
                >
                    <span v-if="!iconTitle" v-html="title"></span>
                    <v-icon v-else v-html="title">iconName</v-icon>
                </v-btn>
            </template>

            <slot />
        </v-menu>
    </div>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
            default: null
        },

        iconTitle: {
            type: Boolean,
            default: false
        }
    }
}
</script>
