<template>
    <ModalLayout :title="$attrs.modal.options.title" :id="$attrs.modal.id">
        <v-container grid-list-lg fluid>
            <TaskCard
                hide-menu
                hide-members
                min-height="90"
                v-for="task in selections"
                :key="task.id"
                :task="task"
                @click.native="select(task)"
                class="mb-2"
            />
        </v-container>
    </ModalLayout>
</template>

<script>
import { Response } from '~/services/events/modal';
import { Task } from '~/services/models';
import UsesTaskStore from '../../mixins/UsesTaskStore';
import TaskCard from '@features/task/components/TaskCard';
import DispatchWhenTokenChanges from '@common/mixins/DispatchWhenTokenChanges';

export default {
    name: 'TasksSelectorModal',

    mixins: [
        UsesTaskStore(),
        DispatchWhenTokenChanges('task/all')
    ],

    components: { TaskCard },

    props: {
        selected: {
            type: Array,
            default: () => ([]),
        },

        onSelect: {
            type: Function,
            required: true
        }
    },

    computed: {
        selections() {
            const selected = Task.collection(this.selected);
            return this.tasks.filter((task) => ! selected.includes(task));
        }
    },

    methods: {
        select(task) {
            this.onSelect(new Response(this, task));
        },
    }
}
</script>
