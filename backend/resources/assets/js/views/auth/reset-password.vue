<template>
    <main>
        <h1 class="text-xs-center mb-1 display-3 font-weight-bold">Sowidu</h1>
        <h2 class="text-xs-center mb-3">You can now set your new password</h2>

        <v-card class="mx-auto">
            <v-card-text class="pa-5">
                <web-form method="post">
                    <template v-slot="{ errors }">
                        <v-alert
                            type="error"
                            class="mb-3"
                            :value="errors.has('password')"
                        >
                            {{ errors.get('password')[0] }}
                        </v-alert>

                        <TextField
                            name="password"
                            type="password"
                            label="New Password"
                        ></TextField>

                        <TextField
                            name="password_confirmation"
                            type="password"
                            label="Confirm New Password"
                        ></TextField>

                        <v-btn type="submit" color="primary" large block>
                            Proceed
                        </v-btn>
                    </template>
                </web-form>
            </v-card-text>
        </v-card>

        <div class="text-xs-center mt-3">&copy; 2020 Sowidu. All rights reserved.</div>
    </main>
</template>

<script>
import WebForm from '~/components/containers/WebForm';

export default {
    layout: 'Guest',

    components: {
        WebForm
    }
}
</script>
