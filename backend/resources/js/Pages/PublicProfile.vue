<template>
  <div>
    <v-card
      color="transparent"
      :width="contentWidth"
      class="mx-auto"
      flat
    >
      <v-container fluid>
        <v-layout>
          <v-flex xs2>
            <v-layout class="pa-0 ma-0" justify-center fill-height>
              <v-avatar size="180">
                <v-img :src="$page.props.profile.photo" />
              </v-avatar>
            </v-layout>
          </v-flex>
          <v-flex>
            <v-card-text>
              <div>
                <div class="headline font-weight-bold mb-2">
                  {{ $page.props.profile.name }}
                </div>

                <div>
                  <div class="mb-3">
                    <span v-if="$page.props.profile.type === 'User'">
                      <v-icon size="16">person</v-icon> Private User
                    </span>

                    <span v-else-if="$page.props.profile.type === 'Company'">
                      <v-icon size="16">apartment</v-icon> Organization
                    </span>

                    <span v-else-if="$page.props.profile.type === 'Employee'">
                      <v-icon size="16">groups</v-icon> Employee
                    </span>
                  </div>

                  <div v-if="$page.props.profile.bio"
                       class="my-3"
                       style="width:55%;"
                       v-text="$page.props.profile.bio"
                  />
                </div>
              </div>
            </v-card-text>

            <v-btn depressed>
              <v-icon left>message</v-icon> Send Message
            </v-btn>

            <v-btn color="green lighten-3" depressed>
              <v-icon left>check</v-icon> Contacts <v-icon>arrow_drop_down</v-icon>
            </v-btn>
          </v-flex>

          <v-flex shrink>
            <v-btn flat>
              <v-icon>settings</v-icon> <v-icon>arrow_drop_down</v-icon>
            </v-btn>
          </v-flex>
        </v-layout>
      </v-container>
    </v-card>

    <v-divider />

    <v-toolbar flat>
      <v-card
        color="transparent" 
        :width="contentWidth"
        class="mx-auto fill-height"
        flat
      >
        <v-layout class="pa-0" fill-height>
          <v-flex xs2 />

          <v-toolbar-items>
            <v-btn flat>About</v-btn>
            <v-btn flat>Organizations</v-btn>
            <v-btn flat>Members</v-btn>
          </v-toolbar-items>
        </v-layout>
      </v-card>
    </v-toolbar>
  </div>
</template>

<script>
import AuthLayout from '../Layouts/AuthLayout.vue';

export default {
    layout: AuthLayout,

    computed: {
        contentWidth() {
            return ({
                'xs': '100%',
                'sm': '100%',
                'md': '80%',
                'lg': '70%',
                'xl': '60%',
            })[this.$vuetify.breakpoint.name];
        },
    },
};
</script>
