/** @flow */

import { Item } from '~/services/models';
import { createContext } from '~/support/factories';

export default (propName: string) => ({
    computed: {
        pickedItems: {
            set(v: Array<Item>) {
                if (! propName) {
                    this.items = v;
                } else {
                    this[propName].items = v;
                }
            },
            get() {
                if (! propName) {
                    return Item.collection(this.items);
                } else {
                    return Item.collection(this[propName].items);
                }
            }
        }
    },

    created() {
        const $this = this;

        this.$purchase = createContext({
            createSlot() {
                $this.pickedItems = $this.pickedItems
                    .insert(Item.create({ quantity: 1 }))
                    .all()
            },

            removeItem(item: Item) {
                $this.pickedItems = $this.pickedItems
                    .remove(item)
                    .all();
            },

            selectItem({ index, value }: { index: number, value: Item }) {
                const collection = $this.pickedItems;

                if (! collection.includes(value)) {
                    const quantity = Math.max(1, value.quantity);

                    collection.splice(index, 1, [
                        Item.create({...value, quantity })
                    ]);
                }

                $this.pickedItems = collection.all();
            },

            changeQty({ index, value }: { index: number, value: number }) {
                const item = $this.pickedItems.slice(index, 1).first();

                item.quantity = Math.max(1, value);

                $this.pickedItems = $this.pickedItems
                    .update(item)
                    .all();
            },
        });
    }
})