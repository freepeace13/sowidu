<template>
    <v-app>
        <v-content>
            <v-container fluid grid-list-lg fill-height>
                <v-layout align-center justify-center column fill-height>
                    <v-progress-circular
                        :size="50"
                        color="primary"
                        indeterminate
                    />
                </v-layout>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
export default {};
</script>
