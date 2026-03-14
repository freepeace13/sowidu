<template>
    <v-layout row fill-height>
        <v-flex shrink>
            <SidebarSettingsMenu />
        </v-flex>
        <v-flex>
            <Suspense :delay="200" class="fill-height">
                <SuspensionSpinner slot="fallback" />

                <router-view></router-view>
            </Suspense>
        </v-flex>
    </v-layout>
</template>

<script>
import SidebarSettingsMenu from '../widgets/SidebarSettingsMenu.vue';
import SuspensionSpinner from '@common/components/SuspensionSpinner';

export default {
    components: {
        SuspensionSpinner,
        SidebarSettingsMenu
    }
}
</script>