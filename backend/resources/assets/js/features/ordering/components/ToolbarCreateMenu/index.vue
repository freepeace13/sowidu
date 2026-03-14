<template>
    <v-menu nudge-bottom="52" nudge-right="-40" min-width="200px">
        <template v-slot:activator="{ on }">
            <v-btn flat v-on="on" icon>
                <v-icon>add</v-icon> 
            </v-btn>
        </template>

        <v-list>
            <v-list-tile
                tag="div"
                v-can="allowCreateIncomingOrder"
                @click="$emit('click:incoming')"
            >
                <v-list-tile-title>
                    {{ $t('buttons.new-incoming-order') }}
                </v-list-tile-title>
            </v-list-tile>

            <v-list-tile
                tag="div"
                v-can="allowCreateOutgoingOrder"
                @click="$emit('click:outgoing')"
            >
                <v-list-tile-title>
                    {{ $t('buttons.new-outgoing-order') }}
                </v-list-tile-title>
            </v-list-tile>
        </v-list>
    </v-menu>
</template>

<script>
import * as Enums from '../../enums';

export default {
    computed: {
        allowCreateIncomingOrder() {
            return Enums.PERMISSIONS.CREATE_INCOMING_ORDER;
        },

        allowCreateOutgoingOrder() {
            return Enums.PERMISSIONS.CREATE_OUTGOING_ORDER;
        }
    }
}
</script>