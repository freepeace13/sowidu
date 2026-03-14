<template>
    <v-responsive :aspect-ratio="16 / 9">
        <canvas
            v-if="page !== undefined"
            :id="`pdf_page_${numPage}`"
        />
        <v-progress-circular
            v-else
            color="grey lighten-1"
            indeterminate
        />
    </v-responsive>
</template>

<script>
export default {
    props: {
        pdf: Object,
        numPage: Number,
    },

    data: () => ({
        page: undefined,
    }),

    created() {
        this.pdf.getPage(this.numPage).then((page) => {
            this.page = page
        })
    },

    mounted() {
        this.$nextTick(() => {
            setTimeout(
                () =>
                    this.renderCanvas(
                        this.$el.querySelector(`#pdf_page_${this.numPage}`),
                    ),
                250,
            )
        })
    },

    methods: {
        renderCanvas(canvas) {
            let viewport = this.page.getViewport({ scale: 1.5 }),
                context = canvas.getContext('2d')

            canvas.height = viewport.height
            canvas.width = viewport.width

            const renderTask = this.page.render({
                canvasContext: context,
                viewport: viewport,
            })

            renderTask.promise.then(() => this.$emit('rendered'))
        },
    },
}
</script>
