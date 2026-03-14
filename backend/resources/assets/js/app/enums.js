import * as ORDER from '@features/ordering/enums';
import * as CONTACT from '@features/contact/enums';
import * as DELIVERY from '@features/delivery/enums';
import * as TASK from '@features/task/enums';
import * as MEDIA from '@features/media/enums';
import * as SETTINGS from '@features/settings/enums';
import * as PRODUCT from '@features/product/enums';
import * as EMPLOYMENT from '@features/employee/enums';

export const APPS = {
    ORDER: ORDER.APP,
    CONTACT: CONTACT.APP,
    DELIVERY: DELIVERY.APP,
    TASK: TASK.APP,
    MEDIA: MEDIA.APP,
    PRODUCT: PRODUCT.APP,
    EMPLOYMENT: EMPLOYMENT.APP
}

export const PERMISSIONS = {
    [APPS.ORDER]: ORDER.PERMISSIONS,
    [APPS.CONTACT]: CONTACT.PERMISSIONS,
    [APPS.DELIVERY]: DELIVERY.PERMISSIONS,
    [APPS.TASK]: TASK.PERMISSIONS,
    [APPS.MEDIA]: MEDIA.PERMISSIONS,
    [APPS.PRODUCT]: PRODUCT.PERMISSIONS,
    [APPS.EMPLOYMENT]: EMPLOYMENT.PERMISSIONS
}