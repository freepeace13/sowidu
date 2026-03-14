/** @flow */

import { mapState } from 'vuex';
import { createContext } from '~/support/factories';
import { Task } from '~/services/models';
import TaskService from '../api';

export default () => ({
    computed: mapState({
        tasks: (state) => state.task.tasks
    }),

    created() {
        this.$tasks = createContext({
            create(instance: Task) {
                return TaskService.create(instance);
            },
            update(instance: Task) {
                return TaskService.update(instance);
            }
        });
    }
})