
export default function (notification) {
    const resource = notification.meta.get('resource');

    switch(resource.type) {
        case 'tasks':
            return { name: resource.type, params: { id: resource.identifier }};

        default:
            return null;
    }
}