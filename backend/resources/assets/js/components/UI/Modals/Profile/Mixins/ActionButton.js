export default {

    computed: {

        addressbookable() {
            return !this.user
                && this.relations['contact:applicable']
                && !this.relations['contact:connected']
                && !this.relations['contact:invited']
                && !this.relations['employment:invited']
        },

        employeable() {
            return !this.user
                && this.relations['employment:applicable']
                && !this.relations['employment:invited']
                && !this.relations['employment:employed']
        },

    },
}