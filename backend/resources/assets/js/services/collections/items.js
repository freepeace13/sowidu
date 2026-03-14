import Item from '~/services/models/item'
import { $snackbar } from '~/services/events/snackbar'
import { compact } from 'lodash';
import { Prop, Types } from '~/support/decorators';


export default class ItemCollection {
    @Prop(Types.SetOf(() => Item)) _collection = [];

    constructor(items) {
        if (items instanceof ItemCollection) {
            this.replace(items.values())
        } else {
            this.replace(items)
        }
    }

    total() {
        return this._collection.reduce(
            (i, c) => parseFloat(i) + parseFloat(c.subtotal), 0
        ).toFixed(2)
    }

    values() {
        return this._collection
    }

    remove(itemId) {
        this.callIfItemIsValid(itemId, (index) => {
            this._collection.splice(index, 1)
        })
    }

    changeQty(itemId, qty = 1) {
        this.callIfItemIsValid(itemId, (index) => {
            let quantity = Math.max(qty, 1)
            let item = this._collection[index]

            this._collection.splice(index, 1, new Item({
                id: item.id,
                quantity,
                price: item.price,
                subtotal: (quantity * parseFloat(item.price)).toFixed(2)
            }))
        })
    }

    async fillItem(index, itemId) {
        await this.callIfCanFill(index, itemId, async () => {
            try {
                // const { data: { data: item } } = await apiCalls.fetchItemById(itemId)
                // const { quantity } = this._collection[index]

                // this._collection.splice(index, 1, new Item({
                //     id: item.id,
                //     quantity,
                //     unit: null,
                //     price: (parseFloat(item.retailPrice)).toFixed(2),
                //     subtotal: (parseFloat(item.retailPrice) * quantity).toFixed(2)
                // }))
            } catch (e) {
                $snackbar.fail(e.message)
            }
        })
    }

    addSlot() {
        this._collection.push(new Item({
            id: null,
            quantity: 1,
            unit: null,
            price: 0,
            subtotal: 0
        }))
    }

    clone() {
        return new ItemCollection(this.values())
    }

    async callIfCanFill(index, itemId, callback) {
        if (Math.max(0, index) > this._collection.length) {
            throw new Error(`Invalid index ${index} for collection length ${this._collection.length}`)
        }

        if (this._collection.findIndex(e => e.id === itemId) !== -1) {
            throw new Error(`Cannot add item twice.`)
        }

        await callback(index, itemId)
    }

    callIfItemIsValid(itemId, callback) {
        let index = this._collection.findIndex(e => e.id === itemId)

        if (index === -1) {
            throw new Error(`Item with id # ${itemId} not exists in the collection.`)
        }

        callback(index)
    }

    replace(items) {
        this._collection = compact(items).map(item => new Item({
            id: item.id,
            quantity: item.quantity || 1,
            name: item.name,
            unit: item.unit,
            price: item.retail_price || item.price,
            subtotal: ((item.quantity || 1) * (item.retail_price || item.price)).toFixed(2)
        }))
    }

    simplified() {
        return this._collection.map(item => ({
            id: item.id, quantity: item.quantity || 1
        }))
    }
}
