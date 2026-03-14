<template>
    <v-app dark>
        <Toolbar>
            <router-view name="toolbar"></router-view>
        </Toolbar>

        <ShortcutDrawer />
    
        <v-content>
            <v-container fluid grid-list-lg
                fill-height
                justify-start
                align-start
                align-content-start
            >
                <v-layout justify-start align-start>
                    <v-flex>
                        <router-view></router-view>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
import Toolbar from '~/components/UI/Toolbar/Toolbar';
import Loader from '~/components/UI/Loader';
import ShortcutDrawer from './UI/Navigation/ShortcutDrawer';
import { compact, last } from 'lodash';
import { capitalize } from '@libs/core/utils/helpers';

export default {
    name: 'auth',

    data: () => ({
        error: {},
    }),

    components: {
        Toolbar,
        Loader,
        ShortcutDrawer
    },

    methods: {
        handleError(error) {
            this.error = error
        }
    },
}
</script>
