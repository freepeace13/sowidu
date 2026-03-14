/** @flow */

import moment from 'moment';
import {  ModelCollection as Collection } from '~/support/wrappers';
import { Model } from '~/support/wrappers';
import { camelKeys, capitalize, arrwrap } from '~/support/helpers';
import type { PropTypes, StatusType } from 'delivery-prop-types';
import { Employee, Customer, Item, Order, Task, Media, resolveFromRaw } from '.';

export default class Delivery extends Model {
    uuid: ?string;
    title: ?string;
    remarks: ?string;
    customer: ?Customer;
    contractor: ?Authenticatable;
    deliveryTime: ?string;
    deliveryDate: ?string;
    type: ?DeliveryType;
    members: Array<Employee> = [];
    items: Array<Item> [];
    orders: Array<Order> = [];
    tasks: Array<Task> = [];
    media: Array<Media> = [];
    status: ?StatusType = {
        deliveryDateOverdue: false,
        deliveryTimeOverdue: false
    };

    constructor(props: PropTypes) {
        super(props);

        this.uuid = props.uuid;
        this.title = props.title;
        this.remarks = props.remarks;
        this.customer = props.customer;
        this.contractor = props.contractor;
        this.type = props.type;
        this.members = props.members;
        this.status = props.status;
        this.deliveryTime = props.deliveryTime;
        this.deliveryDate = props.deliveryDate;
        this.items = props.items;
        this.tasks = props.tasks;
        this.orders = props.orders;
        this.media = props.media;
    }

    // replaceContractorFromContacts(contact: any) {
    //     const someone = new Somebody(contact.originalInfo);
    //     this.contractor = someone.contractor().setMeta({
    //         contactId: contact.id
    //     });
    // }

    schedule() {
        const scheduleString = [this.deliveryDate, this.deliveryTime].join(' ');
        return moment(scheduleString, 'YYYY-MM-DD HH:mm');
    }

    formattedSchedule() {
        return this.schedule().format('YYYY-MM-DD HH:mm:ss')
    }

    toString() {
        return [capitalize(this.type), 'Delivery - ', this.title].join(' ');
    }

    static create(attrs: Object): Delivery {
        const props: PropTypes = camelKeys(attrs);
        const items = arrwrap(props.items);
        const members = arrwrap(props.members);
        const orders = arrwrap(props.orders);
        const tasks = arrwrap(props.tasks);
        const media = arrwrap(props.media);

        return new Delivery({
            ...props,
            items: items.map((v) => Item.create(v)),
            members: members.map((v) => Employee.create(v)),
            tasks: tasks.map((v) => Task.create(v)),
            orders: orders.map((v) => Order.create(v)),
            contractor: props.contractor && resolveFromRaw(props.contractor),
            customer: Customer.create(props.customer),
            media: media.map((v) => Media.create(v))
        });
    }

    static collection(collection: Array<Object>): Collection<Delivery> {
        return new Collection(collection.map((v) => Delivery.create(v)));
    }
}