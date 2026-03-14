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
        </v-layout>
    </view-container>
</template>

<script>
import View from './View.vue'
import PDFPageRenderer from '../PDFPageRenderer.vue'
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
    }),

    mounted() {
        pdfjs.getDocument(this.computedMedia.url).promise.then((pdf) => {
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
        onPageRendered(page) {
            console.log('Page rendered: ', page)
        },
    },
}
</script>
