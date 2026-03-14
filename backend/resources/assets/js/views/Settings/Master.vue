<template>
    <RootView title="Settings" icon="settings">
        <v-layout row>
            <v-flex lg6 offset-lg3 class="pt-0">
                <v-tabs
                    centered
                    color="grey darken-4"
                    slider-color="grey"
                    class="mb-0"
                    icons-and-text
                >
                    <v-tab :to="{ name: 'settings.account' }">
                        Account <v-icon>person</v-icon>
                    </v-tab>

                    <v-tab :to="{ name: 'settings.permission' }" v-if="$store.getters['auth/authorize']('manage permissions')">
                        Permissions <v-icon>lock</v-icon>
                    </v-tab>

                    <v-tab>
                        Notifications <v-icon>notifications</v-icon>
                    </v-tab>
                </v-tabs>

                <v-divider></v-divider>

                <router-view></router-view>
            </v-flex>
        </v-layout>
    </RootView>
</template>
