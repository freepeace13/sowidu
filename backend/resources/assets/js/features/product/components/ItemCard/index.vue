<template>
    <DisplayCard
        :title="item.name"
        :photos="item.media"
        @click:photo="$emit('click:photo', item)"
        @click:title="$emit('click:title', item)"
    >
        <template slot="subtitle">
            <span class="grey--text text-lighten-5">{{ item.longDescription }}</span>
        </template>

        <template slot="menu">
            <v-list light>
                <v-list-tile>
                    <v-list-tile-content @click="$emit('menu:edit', item)">
                        Edit
                    </v-list-tile-content>
                </v-list-tile>
                <v-list-tile>
                    <v-list-tile-content @click="$emit('menu:delete', item)">
                        Delete
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </template>

        <div class="py-2">
            Offered Price
            <span class="float-right green--text">
                {{ item.offeredPrice }}
            </span>
        </div>
        <div class="py-2">
            Fix Traded Price
            <span class="float-right green--text">
                {{ item.fixTradedPrice }}
            </span>
        </div>
        <div class="py-2">
            Retail Price
            <span class="float-right green--text">
                {{ item.retailPrice }}
            </span>
        </div>
    </DisplayCard>
</template>

<script>
import Item from '~/services/models/item';
import DisplayCard from '~/components/layouts/DisplayCard';

export default {
    components: {
        DisplayCard
    },

    props: {
        item: {
            type: Item,
            required: true,
        }
    }
}
</script>