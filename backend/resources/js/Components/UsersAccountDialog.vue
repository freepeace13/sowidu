<template>
    <v-dialog
        v-model="show"
        transition="dialog-bottom-transition"
        fullscreen
        stateless
    >
        <template #activator="props">
            <slot v-bind="props" name="activator" />
        </template>

        <v-card class="hide-overflow" style="position: relative" tile>
            <v-toolbar color="transparent" flat>
                <v-spacer />

                <v-btn icon @click="show = false">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>

            <v-card-text class="py-0">
                <users-account />
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
import UsersAccount from './UsersAccount.vue'

export default {
    components: {
        UsersAccount,
    },

    data: () => ({
        show: false,
    }),
}
</script>
