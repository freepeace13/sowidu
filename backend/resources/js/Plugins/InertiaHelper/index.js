import Vue from 'vue'

export default {
    methods: {
        $pagePropsRemove(props) {
            if (!props)
                throw 'Please provide prop keys, could be a string an array.'

            if (Array.isArray(props))
                props.map((prop) => Vue.delete(this.$page.props, prop))

            Vue.delete(this.$page.props, props) // `props` is String
        },
    },
}
