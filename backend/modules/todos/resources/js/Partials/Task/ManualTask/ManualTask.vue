<template>
    <div>
        <ManualTaskModal
            ref="manualTaskModal"
            :members="members"
        />
    </div>
</template>

<script>
import ManualTaskModal from './ManualTaskModal.vue'

export default {
    components: { ManualTaskModal },
    props: {
        members: {
            type: Array,
            required: false,
        },
    },
    mounted() {
        this.$root.$on('manual-task.open', () => {
            this.show()
        })
    },
    beforeDestroy() {
        this.$root.$off('manual-task.open')
    },
    methods: {
        show() {
            this.$refs.manualTaskModal.show(this.members)
        },
    },
}
</script>
