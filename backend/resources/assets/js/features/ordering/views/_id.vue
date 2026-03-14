<template>
    <v-layout row justify-space-between>
        <v-flex>
            <router-view></router-view>
        </v-flex>
        <v-flex shrink>
            <ShowSidebarWidget />
        </v-flex>
    </v-layout>
</template>

<script>
import SuspensionSpinner from '@common/components/SuspensionSpinner';
import StateProgress from '../components/StateProgress';
import ModifiesOrder from '../mixins/ModifiesOrder';
import ShowSidebarWidget from '../widgets/ShowSidebar.vue';
import { createResource } from 'vue-async-manager';

export default {
    mixins: [
        ModifiesOrder(),
    ],

    components: {
        ShowSidebarWidget,
        StateProgress,
        SuspensionSpinner
    },

    created() {
        this.$rm = createResource((id) => this.initialize(id));
        this.$rm.read(this.$route.params.id);
    },
}
</script>
