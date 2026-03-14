<template>
    <v-list>
        <menu-list-item
            icon="restore"
            @click="$emit('click:restore')"
        >
            {{ $t('buttons.restore') }}
        </menu-list-item>

        <menu-list-item
            icon="delete_forever"
            @click="$emit('click:delete')"
        >
            {{ $t('buttons.delete-forever') }}
        </menu-list-item>
    </v-list>
</template>

<script>
import MenuListItem from '../MenuListItem.vue';

export default {
    components: { MenuListItem },

    props: {
        media: {
            type: Object,
            required: true,
        },
    },
};
</script>
