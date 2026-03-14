import ModelNotExists from '~/exceptions/ModelNotExists';
import { Model } from '~/support/wrappers';
import { isNullOrUndefined } from '~/support/helpers';
import Registrar from './registrar';

import Task from './task';
import Delivery from './delivery';
import Order from './order';
import Media from './media';
import Employee from './employee';
import User from './user';
import Company from './company';
import Address from './address';
import Item from './item';
import Customer from './customer';
import Contact from './contact';
import Notification from './notification';
import Invitation from './invitation';
import Comment from './comment';

Registrar.boot({
    companies: Company,
    employees: Employee,
    users: User,
    orders: Order,
    deliveries: Delivery,
    tasks: Task,
    address: Address,
    contacts: Contact,
    customers: Customer,
    media: Media,
    items: Item,
    invitations: Invitation,
    comments: Comment,
});

export function resolveFromRaw(v: { alias: string }): Model {
    const $class = Registrar.getQualifiedClass(v);

    if (! $class) {
        throw ModelNotExists.fromRaw(v.alias, Object.keys(Registrar.getModels()));
    }

    return $class.create(v);
}

export {
    Address,
    Company,
    Employee,
    Task,
    Delivery,
    Order,
    Media,
    Contact,
    Customer,
    Comment,
    Item,
    User,
    Notification,
    Invitation
}