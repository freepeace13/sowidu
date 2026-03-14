/** @flow */

import { showTasksSelector } from '~/services/events/modal';
import { Task } from '~/services/models';

export default (propName: string) => ({
    methods: {
        removeTask(task: Task) {
            this[propName].tasks = Task
                .collection(this[propName].tasks)
                .remove(task)
                .all();
        },

        browseTasks() {
            showTasksSelector({
                selected: this[propName].tasks,
                onSelect: (response) => {
                    this[propName].tasks = Task
                        .collection(this[propName].tasks)
                        .insert(response.value)
                        .all();

                    response.close();
                }
            }, true);
        }
    }
});