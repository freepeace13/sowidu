<template>
    <v-toolbar height="64" card>
        <v-toolbar-items>
            <v-tabs
                height="64"
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
        </v-toolbar-items>

        <v-spacer></v-spacer>

        <v-btn icon v-can="allowCreateTask" @click="createTask">
            <v-icon>add</v-icon>
        </v-btn>
    </v-toolbar>
</template>

<script>
import { showTaskModal } from '~/services/events/modal';
import HandlesAuthorizations from '~/components/Mixins/HandlesAuthorizations';
import { PERMISSIONS as TaskPermissions } from '@features/task/enums';

export default {
    name: 'IndexTabs',

    mixins: [HandlesAuthorizations()],

    computed: {
        allowCreateTask() {
            return TaskPermissions.CREATE_TASK;
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