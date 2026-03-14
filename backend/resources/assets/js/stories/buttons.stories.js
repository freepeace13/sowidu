import UploadButton from '~/components/UI/Buttons/UploadButton';
import PopoverMenu from '~/components/UI/PopoverMenu';

export default {
    title: 'Buttons'
}

export const popoverMenuButton = () => ({
    components: {
        PopoverMenu
    },

    template: `
        <application>
            <PopoverMenu>
                <template v-slot:activator="{ on }">
                    <v-btn color="primary" v-on="on">Menu as Popover</v-btn>
                </template>

                <v-list>
                    <v-list-tile>
                        <v-list-tile-content>Menu 1</v-list-tile-content>
                    </v-list-tile>
                    <v-list-tile>
                        <v-list-tile-content>Menu 2</v-list-tile-content>
                    </v-list-tile>
                </v-list>
            </PopoverMenu>
        </application>
    `
});

export const uploadButton = () => ({
    components: {
        UploadButton
    },

    data: () => ({
        files: []
    }),

    template: `
        <application>
            <pre v-for="file in files" class="black--text">
                {{ file.name }}
            </pre>

            <UploadButton
                color="success"
                @upload="(e) => files = e.target.files"
                multiple
            >
                Browse
            </UploadButton>
        </application>
    `
})