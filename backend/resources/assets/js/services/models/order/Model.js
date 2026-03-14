/** @flow */

import { camelKeys, capitalize, arrwrap } from '~/support/helpers';
import {  ModelCollection as Collection } from '~/support/wrappers';
import { Model } from '~/support/wrappers';
import type { PropTypes } from 'order-prop-types';
import { Customer, Task, Delivery, Employee, Media, Item, resolveFromRaw } from '..';
import createStates from '../mixins/hasStates';
import { ProgressState } from './states';
import OrderPolicies from './Policies';

const HasStates = createStates({
    state: new ProgressState
});

export default class Order extends HasStates(Model) {
    uuid: string;
    description: string;
    orderNumber: number;
    customer: Customer;
    type: OrderType;
    creator: Authorizable;
    contractor: Authenticatable;
    tasks: Array<Task> = [];
    items: Array<Item> = [];
    media: Array<Media> = [];
    deliveries: Array<Delivery> = [];
    members: Array<Employee> = [];
    formattedDates: {
        deliveryDate: string,
        orderDate: string
    };

    constructor(props: PropTypes) {
        super(props);

        this.uuid = props.uuid;
        this.description = props.description;
        this.orderNumber = props.orderNumber;
        this.customer = props.customer;
        this.creator = props.creator;
        this.type = props.type;
        this.contractor = props.contractor;
        this.items = props.items;
        this.tasks = props.tasks;
        this.deliveries = props.deliveries;
        this.media = props.media;
        this.members = props.members;
        this.formattedDates = {
            ...props.formattedDates,
            ...Object.assign({}, props.formattedDates)
        };
    }

    get policies(): OrderPolicies {
        return new OrderPolicies(this);
    }

    get title() {
        return `Order No. ${this.orderNumber}`;
    }

    toString() {
        return `${capitalize(this.type)} Order No. ${this.orderNumber}`
    }

    static create(attrs: Object): Order {
        const props: PropTypes = camelKeys(attrs);

        return new Order({
            ...props,
            customer: props.customer ? resolveFromRaw(props.customer) : null,
            items: arrwrap(props.items).map((v) => Item.create(v)),
            contractor: props.contractor && resolveFromRaw(props.contractor),
            members: arrwrap(props.members).map((v) => Employee.create(v)),
            media: arrwrap(props.media).map((v) => Media.create(v)),
            deliveries: arrwrap(props.deliveries).map((v) => Delivery.create(v)),
            tasks: arrwrap(props.tasks).map((v) => Task.create(v)),
        });
    }

    static collection(collection: Array<Object>): Collection<Order> {
        return new Collection(collection.map((v) => Order.create(v)));
    }
}
