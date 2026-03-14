<template>
    <div>
        <TimeLogsModal
            ref="timeLogsModal"
            @open:form="(timeLog = null) => showForm(timeLog)"
        />
        <TimeLogForm
            ref="timeLogForm"
            :task="task"
        />
    </div>
</template>
<script>
import TimeLogForm from './TimeLogForm.vue'
import TimeLogsModal from './TimeLogsModal.vue'

export default {
    components: { TimeLogForm, TimeLogsModal },

    props: {
        task: {
            required: false,
            type: Object,
            default: () => ({}),
        },
    },

    methods: {
        showForm(timeLog = null) {
            this.$refs.timeLogForm.show(timeLog)
        },

        showTimeLogs() {
            this.$refs.timeLogsModal.show(this.task)
        },
    },
}
</script>
