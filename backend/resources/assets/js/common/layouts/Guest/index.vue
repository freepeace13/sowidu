<template>
    <v-app dark>
        <v-content>
            <v-container fluid>
                <v-layout fill-height align-center row wrap>
                    <v-flex sm8 offset-sm2 md6 offset-md3 lg4 offset-lg4 xl2 offset-xl5>
                        <transition name="page" mode="out-in">
                            <router-view></router-view>
                        </transition>
                    </v-flex>

                    <v-flex xs12 class="text-xs-center">
                        <LocaleSetter />
                    </v-flex>
                </v-layout>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
import LocaleSetter from '@app/containers/LocaleSetter';

export default {
    components: { LocaleSetter }
}
</script>
