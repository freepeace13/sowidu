declare module 'task-api-payload' {
    declare export type APIPayload = {
        id?: number,
        title: string,
        description: ?string,
        members: Array<number>,
        deliveries: Array<number>,
        orders: Array<number>,
        media: Array<number>
    }
}

declare module 'task-service-payload' {
    declare export type ServicePayload = {
        title: string,
        description: ?string,
        members: MemberCollection,
        deliveries: Array<Delivery>,
        orders: Array<Order>,
        media: Array<Media>
    }
}

declare module 'task-prop-types' {
    declare export type PropTypes = {
        id: ?number,
        title: ?string,
        description: ?string,
        members: Array<Employee>,
        media: Array<Media>,
        orders: Array<Order>,
        deliveries: Array<Delivery>,
        creator: Authorizable,
        states: Object,
        formattedDates: {
            dateCreated: ?string,
            timeCreated: ?string,
            startedAt: ?string,
            endedAt: ?string
        },
        createdAt: any,
        updatedAt: any,
        alias: any,
    }
}