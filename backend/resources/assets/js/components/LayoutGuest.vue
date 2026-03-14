<template>
    <v-app dark>
        <v-container fluid>
            <v-layout fill-height align-center>
                <v-flex xs4 offset-xs4>
                    <transition name="page" mode="out-in">
                        <router-view></router-view>
                    </transition>
                </v-flex>
            </v-layout>
        </v-container>
    </v-app>
</template>

<script>
    export default {
        name: 'guest'
    }
</script>
