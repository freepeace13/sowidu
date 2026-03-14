<template>
    <PopoverMenu>
        <template v-slot:activator="{ on }">
            <slot v-on="on"></slot>
        </template>

        <v-list>
            <v-list-tile>
                <v-list-tile-content>Menu 1</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
                <v-list-tile-content>Menu 2</v-list-tile-content>
            </v-list-tile>
        </v-list>
    </PopoverMenu>
</template>

<script>
import PopoverMenu from '~/components/layouts/PopoverMenu';
import { Delivery } from '~/services/models';
import UsesDeliveryStore from '~/components/Mixins/UsesDeliveryStore';

export default {
    name: 'DeliveryBrowser',

    components: { PopoverMenu },

    mixins: [UsesDeliveryStore()],

    props: {
        selected: {
            type: Array,
            default: () => ([]),
            validator(prop) {
                return prop.every((v) => v instanceof Delivery);
            }
        }
    }
}
</script>