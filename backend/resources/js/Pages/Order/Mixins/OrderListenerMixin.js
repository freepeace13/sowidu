export default {
    mounted() {
        const { id: order } = this.order

        window.Echo.private(`orders.${order}`).listenToAll(() => {
            this.$inertia.reload({
                preserveState: true,
                preserveScroll: true,
                only: [
                    'medias',
                    'notifications',
                    'documents',
                    'invoices',
                    'requiresResponse',
                    'outgoingOrders',
                    'incomingOrders',
                ],
            })
        })
    },

    beforeDestroy() {
        const { id: order } = this.order

        window.Echo.leave(`orders.${order}`)
    },
}
