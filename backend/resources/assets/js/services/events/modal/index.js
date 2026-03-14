import { DEFAULT_CONFIG, MODULES, MODAL_SIZES } from '~/services/events/constants'
import { fetchAttachables } from '~/services/api/attachment'
import Task from '~/services/models/task'
import _terminal from '~/services/events'
import { DELIVERY_TYPES } from '~/support/constants'
import Contact from '~/services/models/contact'
import { RoleCollection, PermissionCollection } from '~/services/collections/authorization'
import { lazy } from 'vue-async-manager'

export const defaultConfig = DEFAULT_CONFIG[MODULES.MODAL]

export const $modal = {
    ..._terminal.modal,
    createResponse(...args) {
        return new Response(...args)
    }
}

export const Response = class {
    constructor(modal, value = null) {
        this._modal = modal
        this.value = value
    }

    close() {
        $modal.close(this._modal.$vnode.key)
    }
}

export const showPermissionsManagerModal = (employeeId, append = false) => {
    (append ? $modal.append : $modal.show)({
        ...defaultConfig,
        title: 'Roles &amp; Permissions',
        modal: require('~/components/UI/Modals/Permission/RolePermission').default,
        attrs: { employeeId }
    })
}

export const showAttachableExplorer = ({
    selected,
    filter,
    ...listeners
}, append = false) => {
    (append ? $modal.append : $modal.show)({
        ...defaultConfig,
        size: MODAL_SIZES.XLARGE,
        title: `Document Explorer`,
        modal: require(`~/components/UI/Modals/DocumentExplorer`).default,
        attrs: {
            selected,
            fetchAttachables,
            filter
        },
        listeners
    })
}

export const showTaskModal = ({ taskId, onSuccess, ...listeners } = {}, append = false) => {
    (append ? $modal.append : $modal.show)({
        ...defaultConfig,
        title: 'Task',
        modal: require(`@features/task/modals/TaskForm`).default,
        attrs: { taskId, onSuccess },
        listeners
    });
}

export const showDelivery = ({ type, deliveryId, onSuccess, ...listeners }, append = false) => {
    const componentName = (type === DELIVERY_TYPES.INCOMING)
        ? 'IncomingDeliveryForm'
        : 'OutgoingDeliveryForm';

    (append ? $modal.append : $modal.show)({
        ...defaultConfig,
        size: MODAL_SIZES.LARGE,
        title: 'Delivery',
        modal: require(`@features/delivery/modals/${componentName}`).default,
        attrs: { deliveryId, onSuccess },
        listeners
    })
}

export const showCompanyContact = (companyId, append = false) => {
    (append ? $modal.append : $modal.show)({
        ...defaultConfig,
        title: 'Company Contact',
        modal: require(`@features/contact/modals/CompanyForm`).default,
        attrs: { companyId }
    });
}

export const showEmployeeContact = (employeeId, append = false) => {
    (append ? $modal.append : $modal.show)({
        ...defaultConfig,
        title: 'Employee Contact',
        modal: require(`@features/contact/modals/EmployeeForm`).default,
        attrs: { employeeId }
    })
}

export const showUserContact = (userId, append = false) => {
    (append ? $modal.append : $modal.show)({
        ...defaultConfig,
        title: 'User Contact',
        modal: require(`@features/contact/modals/UserForm`).default,
        attrs: { userId }
    })
}

export const showNote = (message, onProceed, append = false) => {
    (append ? $modal.append : $modal.show)({
        ...defaultConfig,
        title: "Note",
        size: MODAL_SIZES.SMALL,
        modal: require('@features/contact/modals/InvitationMessageForm').default,
        attrs: { message, onProceed },
    });
}

export const showMediaSelector = ({ selected, onSelect }, append = false) => {
    (append ? $modal.append : $modal.show)({
        ...defaultConfig,
        fullscreen: true,
        title: "Media Library",
        modal: require('@features/media/modals/MediaSelector').default,
        attrs: { selected, onSelect },
    });
}

export const showContactSelector = ({ onSelect }, append = false) => {
    (append ? $modal.append : $modal.show)({
        ...defaultConfig,
        title: 'Contacts',
        modal: require('@features/contact/modals/ContactSelector').default,
        attrs: { onSelect }
    })
}

export const showTasksSelector = ({ selected, onSelect }, append = false) => {
    (append ? $modal.append : $modal.show)({
        ...defaultConfig,
        title: 'Tasks',
        modal: require('@features/task/modals/TaskSelector').default,
        attrs: { selected, onSelect }
    })
}

export const showDeliverySelector = ({ selected, onSelect }, append = false) => {
    (append ? $modal.append : $modal.show)({
        ...defaultConfig,
        title: 'Delivery',
        modal: require('@features/delivery/modals/DeliverySelector').default,
        attrs: {
            selected,
            onSelect
        }
    })
}

export const showOrderSelector = ({ selected, onSelect }, append = false) => {
    (append ? $modal.append : $modal.show)({
        ...defaultConfig,
        title: 'Order',
        modal: require('@features/ordering/modals/OrderSelector').default,
        attrs: { selected, onSelect }
    });
}

export const showItemModal = ({ itemId, onSuccess, ...listeners } = {}, append = false) => {
    (append ? $modal.append : $modal.show)({
        ...defaultConfig,
        title: 'Product',
        modal: require('@features/product/modals/ItemForm').default,
        attrs: { itemId, onSuccess },
        listeners
    })
}

export const showEmployeeSelector = (selected, onSelect, append = false) => {
    (append ? $modal.append : $modal.show)({
        ...defaultConfig,
        title: 'Employee Explorer',
        modal: require('@features/account/modals/EmployeeSelector').default,
        attrs: { selected, onSelect }
    })
}


export const showMedia = (mediaId, append = false) => {
    (append ? $modal.append : $modal.show)({
        ...defaultConfig,
        modal: require('@features/media/modals/MediaForm').default,
        attrs: { mediaId }
    })
}