<template>
    <div class="items-grid">
        <slot v-for="(item, index) in purchasedItems"
            :index="index"
            :item="item"
        />

        <v-btn
            v-if="!readonly"
            color="primary"
            class="mx-0 my-5"
            @click="$emit('create-slot')"
        >
            Add Item
        </v-btn>
    </div>
</template>

<script>
import { Employee } from '~/services/models';

export default {
    name: 'ItemsGrid',

    props: [
        'purchasedItems',
        'readonly'
    ],
}
</script>
