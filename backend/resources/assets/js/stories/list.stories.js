import PopoverMenu from '~/components/UI/PopoverMenu';

export default {
    title: 'Listing'
}

export const popoverMenuFromList = () => ({
    components: {
        PopoverMenu
    },

    template: `
        <application>
            <div class="half-width">
                <v-list>
                    <PopoverMenu>
                        <v-list-tile slot="activator" slot-scope="{ on }" v-on="on">
                            <v-list-tile-content>I popover menus</v-list-tile-content>
                        </v-list-tile>

                        <v-list>
                            <v-list-tile>
                                <v-list-tile-content>Menu 1</v-list-tile-content>
                            </v-list-tile>
                            <v-list-tile>
                                <v-list-tile-content>Menu 2</v-list-tile-content>
                            </v-list-tile>
                        </v-list>
                    </PopoverMenu>

                    <v-list-tile>
                        <v-list-tile-content>I don't have menu</v-list-tile-content>
                    </v-list-tile>
                </v-list>
            </div>
            
        </application>
    `
})