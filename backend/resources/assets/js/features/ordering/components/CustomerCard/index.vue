<template>
    <v-card>
        <v-list v-if="activeSelector">
            <v-list-tile>
                <CustomerSelector @change="$emit('change', $event)" />
            </v-list-tile>
        </v-list>
        
        <v-list v-else>
            <v-list-tile avatar>
                <v-list-tile-avatar>
                    <img :src="customer.avatar.url" />
                </v-list-tile-avatar>

                <v-list-tile-content>
                    <v-list-tile-title>{{ customer.name }}</v-list-tile-title>
                    <v-list-tile-sub-title>{{ customer.email }}</v-list-tile-sub-title>
                </v-list-tile-content>

                <v-list-tile-action>
                    <v-btn icon @click="$emit('click:edit', $event)">
                        <v-icon>edit</v-icon>
                    </v-btn>
                </v-list-tile-action>
            </v-list-tile>

            <v-list-tile>
                <v-list-tile-avatar>
                    <v-icon>location_on</v-icon>
                </v-list-tile-avatar>
                <v-list-tile-content>
                    <v-list-tile-sub-title>
                        {{ customer.address.label || 'Not set' }}
                    </v-list-tile-sub-title>
                </v-list-tile-content>
            </v-list-tile>
        </v-list>
    </v-card>
</template>

<script>
import CustomerSelector from '@common/components/CustomerSelector';
import Employee from '~/services/models/employee';
import User from '~/services/models/user';

export default {
    components: {
        CustomerSelector
    },

    props: {
        activeSelector: {
            type: Boolean,
            default: false
        },
    
        customer: {
            type: [Employee, User],
            required: true
        }
    }
}
</script>
