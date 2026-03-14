<template>
    <div class="board-actions-container">
        <v-btn
            v-for="(action, index) in actions"
            :key="index"
            :class="buttonClass"
            flat
            :disabled="action.disabled"
            class="action-button"
            @click="triggerAction(action)"
        >
            <v-icon
                size="19"
                class="mr-1"
                >{{ action.icon }}</v-icon
            >
            {{ action.label }}
        </v-btn>
    </div>
</template>

<script>
export default {
    props: {
        buttonClass: {
            type: String,
            default:
                'grey--text text--darken-2 grey lighten-2 text-capitalize text-md-left task-sidebar-options',
        },
        manualTaskRef: {
            type: Object,
            required: false,
        },
        disabledActions: {
            type: Array,
            default: () => [],
        },
    },
    computed: {
        board() {
            return this.$page.props.board?.id
        },
        actions() {
            return [
                {
                    label: 'Add Manual Task',
                    icon: 'playlist_add',
                    handler: () => this.$root.$emit('manual-task.open'),
                    disabled: this.disabledActions.includes('manual-task'),
                },
                {
                    label: 'Go to Tasks',
                    icon: 'arrow_forward',
                    handler: () =>
                        this.$inertia.get(
                            this.$route('todos.boards.task.tasks-group', {
                                board: this.board,
                            }),
                        ),
                    disabled: this.disabledActions.includes('go-to-tasks'),
                },
            ].filter((action) => !action.disabled)
        },
    },
    methods: {
        triggerAction(action) {
            if (action.handler) {
                action.handler()
            }
        },
    },
}
</script>
