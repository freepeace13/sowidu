export default {
    data: () => ({
        isShow: false,
        x: 0,
        y: 0,
    }),

    methods: {
        show(e) {
            e.preventDefault()
            this.isShow = false
            this.x = e.clientX
            this.y = e.clientY
            this.$nextTick(() => {
                this.isShow = true
            })
        },
    },
}
