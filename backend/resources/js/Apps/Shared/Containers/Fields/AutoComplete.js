export default {
    name: 'AutoComplete',

    /** The provider component */
    provider: null,

    /** The field component factory function */
    field: () => {},

    render(h) {
        return h(this.$options.provider, {
            scopedSlots: {
                default: (props) => {
                    const factory = (component, context = {}) => {
                        return h(component, {
                            attrs: {
                                ...this.$attrs,
                                ...context.attrs,
                                items: props.items,
                                loading: props.loading,
                            },
                            on: {
                                ...this.$listeners,
                                ...context.on,
                            },
                            scopedSlots: {
                                ...this.$scopedSlots,
                            },
                        });
                    }

                    return this.$options.field(factory, props)
                },
            },
        })
    },
}