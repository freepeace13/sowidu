<template>
    <v-menu
        offset-y
        z-index="11"
        allow-overflow
        content-class="max-h-80-screen"
    >
        <template #activator="{ on }">
            <v-btn icon v-on="on">
                <v-icon>apps</v-icon>
            </v-btn>
        </template>

        <v-card max-width="300">
            <AppsNavList />
        </v-card>
    </v-menu>
</template>

<script>
import AppsNavList from '../../Components/AppsNavList.vue'

export default {
    components: {
        AppsNavList,
    },
}
</script>
