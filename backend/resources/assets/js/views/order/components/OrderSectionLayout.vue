<template>
    <section>
        <ActionsToolbar :title="title">
            <template slot="actions" v-if="!hideActions">
                <slot name="actions"></slot>
            </template>
        </ActionsToolbar>

        <slot></slot>
    </section>
</template>

<script>
import ActionsToolbar from './ActionsToolbar';

export default {
    name: 'OrderSectionLayout',

    components: { ActionsToolbar },

    props: {
        title: {
            type: String,
            required: true
        },

        hideActions: {
            type: Boolean,
            default: false
        }
    }
}
</script>