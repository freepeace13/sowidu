export default (props) => ({
    async beforeEnter(to, from, next) {
        const [component] = to.matched;
    
        const generator = (async function* () {
            for (const [ name, instance ] of Object.entries(component.components)) {
                let value = {};

                if (typeof instance.fetch === 'function') {
                    value = await instance.fetch();
                }

                yield { name, value };
            }
        });

        const props = {};

        for await (const prop of generator()) {
            props[prop.name] = prop.value;
        }

        Object.assign(to.meta, { props });

        next();
    },

    props: Object.keys(props).reduce((result, name) => {
        result[name] = (route) => ({ ...route.meta.props[name], ...props[name](route) });
        return result;
    }, {})
});