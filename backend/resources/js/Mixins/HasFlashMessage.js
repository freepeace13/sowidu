export default {
    computed: {
        hasFlash() {
            return (
                this.$page.props.flash?.type && this.$page.props.flash?.message
            )
        },

        flash() {
            return this.$page.props.flash
        },
    },
}
