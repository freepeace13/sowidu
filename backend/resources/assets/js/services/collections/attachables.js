import { findIndex } from 'lodash';
import { compact } from 'lodash';
import { Prop, Types } from '~/support/decorators';

export default class AttachableCollection {
    @Prop(Types.SetOf(() => Attachable)) _collection = [];

    constructor(...args) {
        this.replace(...args);
    }

    values() {
        return this._collection
    }

    remove(item) {
        if (this.exists(item)) {
            this._collection.splice(this.indexOf(item), 1)
        }
    }

    exists(item) {
        const { docType, id } = this.createAttachable(item)
        return findIndex(this._collection, { docType, id }) !== -1
    }

    indexOf(item) {
        const { docType, id } = this.createAttachable(item)
        return findIndex(this._collection, { docType, id })
    }

    replace(attachables) {
        this._collection = compact(attachables).map(
            item => this.createAttachable(item)
        )
    }

    clone() {
        return new AttachableCollection(this.values())
    }

    isEmpty() {
        return !this._collection.length > 0
    }

    createAttachable(cols) {
        return new Attachable(cols);
    }

    add(item) {
        if (!this.exists(item)) {
            this._collection.push(this.createAttachable(item))
        }
    }

    simplified() {
        return this._collection.map(attachable => ({
            id: attachable.id,
            doc_type: attachable.docType
        }))
    }

    toggle(item) {
        if (this.exists(item)) {
            this.remove(item)
        } else {
            this.add(item)
        }
    }
}
