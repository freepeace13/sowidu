export default {
    props: {
        translations: {
            type: Object,
            required: true,
        },
        locale: {
            type: String,
            required: true,
        },
    },

    created() {
        this.$i18n.mergeLocaleMessage(
            this.locale,
            this.translations[this.locale],
        )
    },
}
