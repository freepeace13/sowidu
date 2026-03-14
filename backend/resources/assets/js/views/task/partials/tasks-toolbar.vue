<template>
    <v-toolbar-extension title="TASKS">
        <v-spacer></v-spacer>

        <v-tabs
            height="64"
            align-with-title
            slider-color="grey"
            color="transparent"
            active-class="font-weight-bold"
        >
            <v-tab exact :to="{ name: 'tasks', query: { group: 'pending' } }" replace>
                Pending
            </v-tab>

            <v-tab
                exact
                :to="{ name: 'tasks', query: { group: 'finished' } }"
                replace
                ripple
            >
                Finished
            </v-tab>

            <v-tab
                class="red--text"
                exact
                :to="{ name: 'tasks', query: { group: 'archived' } }"
                replace
                ripple
            >
                Archived
            </v-tab>
        </v-tabs>

        <v-btn icon v-if="allowCreateTask" @click="createTask">
            <v-icon>add</v-icon>
        </v-btn>
    </v-toolbar-extension>
</template>

<script>
import { showTaskModal } from '~/services/events/modal';
import HandlesAuthorizations from '~/components/Mixins/HandlesAuthorizations';
import vToolbarExtension from '~/components/UI/Toolbar/v-toolbar-extension';

export default {
    name: 'tasks-toolbar',

    components: { vToolbarExtension },

    mixins: [HandlesAuthorizations()],

    computed: {
        allowCreateTask() {
            return this.currentIsUser || this.authorizeTo('create task');
        }
    },

    methods: {
        createTask() {
            if (this.allowCreateTask) {
                showTaskModal();
            }
        }
    }
}
</script>