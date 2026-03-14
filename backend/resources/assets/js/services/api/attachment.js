import axios, { post, get } from 'axios'
import { resolveResponseData } from '~/services/utils'
import { DOCTYPES } from '~/support/constants';
import Order from '~/services/models/order';
import Task from '~/services/models/task';
import Delivery from '~/services/models/delivery';

export const resolveAttachableResource = (resource) => {
    switch (resource.docType || resource.doc_type) {
        case DOCTYPES.ORDER:
            return new Order(resource);
        case DOCTYPES.DELIVERY:
            return new Delivery(resource);
        case DOCTYPES.TASK:
            return new Task(resource);
    }

    return resource;
}

export const fetchAttachables = async (options = {}) => {
    const response = await get('/api/attachables')
    return resolveResponseData(response, resource => resource)
}

export const createAttachment = async (payload, options = {}) => {
    return resolveResponseData(
        await post(`/api/attachments`, payload, options),
        resolveAttachableResource
    )
}

export const attachTargetFromSource = (
    source = {}, target = {}, options = {}
) => {
    return createAttachment({
        source: {
            doc_type: source.docType,
            id: source.id
        },
        target: {
            doc_type: target.docType,
            id: target.id
        }
    }, options);
}

export const removeAttachment = async (payload, options = {}) => {
    return resolveResponseData(
        await axios.delete(`/api/attachments`, payload, options),
        resolveAttachableResource
    )
}
