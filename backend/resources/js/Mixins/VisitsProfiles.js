export default {
    methods: {
        visitProfile(resource) {
            const type = {
                User: 'users',
                Company: 'companies',
                Employee: 'employees',
            }[resource.type];

            window.location.href = this.$route(`apps.contacts.${type}.show`, {
                [resource.type.toLowerCase()]: resource.id,
            });

            // this.$inertia.visit(this.$route(`apps.contacts.${type}.show`, {
            //     [resource.type.toLowerCase()]: resource.id,
            // }));
        },
    },
};