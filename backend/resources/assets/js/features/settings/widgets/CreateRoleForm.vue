<template>
    <v-card class="grey darken-3 mb-3">
        <v-card-text>
            <InputField
                v-model="newRole"
                label="NEW ROLE"
                :hide-details="!$roles.$errors.has('name')"
                :errors="$roles.$errors.get('name', [])"
                :loading="$roles.$loading"
                append-icon="add"
                @click:append="$roles.create(newRole)"
            />
        </v-card-text>
    </v-card>
</template>

<script>
import InputField from '@common/components/InputField';
import createContext from '@common/utils/ContextManager';
import { RoleService } from '../api';

export default {
    components: {
        InputField
    },

    data: () => ({
        newRole: null,
    }),

    created() {
        const self = this;

        this.$roles = createContext({
            async create(role) {
                try {
                    const result = await RoleService.create(role);
                    self.$emit('created', result);
                    self.newRole = null;
                } catch (error) {
                    self.$roles.$errors = error;
                }
            }
        });
    }
}
</script>