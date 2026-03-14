<template>
    <view-container scrollable>
        <v-layout
            fill-height
            justify-start
            align-center
            column
        >
            <PDFPageRenderer
                v-for="page in pages"
                :key="page.numPage"
                :pdf="pdf"
                :num-page="page.numPage"
                @rendered="onPageRendered(page)"
            />
            <v-progress-circular
                v-show="isLoading"
                indeterminate
                color="primary"
                size="64"
                class="loading"
            />
        </v-layout>
    </view-container>
</template>
<script>
import View from '../View.vue'
import PDFPageRenderer from '@components/PDFPageRenderer.vue'
import pdfjs from '@bundled-es-modules/pdfjs-dist/build/pdf'

pdfjs.GlobalWorkerOptions.workerSrc = new URL(
    '@bundled-es-modules/pdfjs-dist/build/pdf.worker.js',
    import.meta.url,
).toString()

export default {
    components: {
        PDFPageRenderer,
    },

    extends: View,

    data: () => ({
        pdf: null,
        pages: [],
        isLoading: false,
    }),

    mounted() {
        this.isLoading = true
        pdfjs.getDocument(this.computedAttachment.url).promise.then((pdf) => {
            this.pdf = pdf
            this.$nextTick(() => {
                ;[...Array(pdf.numPages).keys()].forEach((numPage) => {
                    this.pages.push({
                        numPage: numPage + 1,
                    })
                })
            })
        })
    },

    methods: {
        onPageRendered() {
            this.isLoading = false
        },
    },
}
</script>
<style lang="scss" scoped>
.loading {
    position: fixed;
    top: 44%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
</style>
