import VideoPlayer from '~/components/VideoPlayer';
import store from '~/services/store';
import faker from 'faker';

const sampleImages = require('./data/images.json');

const __galleryItems = sampleImages.map((url) => ({
    title: faker.lorem.sentence(),
    description: faker.lorem.paragraph(),
    src: url,
    type: 'image/png'
})).concat([{
    title: faker.lorem.sentence(),
    description: faker.lorem.paragraph(),
    src: 'http://localhost:8000/samplevid.mp4',
    type: 'video/mp4'
}]);

export default {
    title: 'Dialogs'
}

export const gallery = () => ({
    methods: {
        openGallery() {
            store.dispatch('ui/gallery/open', {
                sources: __galleryItems,
                current: 1
            });
        }
    },

    template: `
        <application>
            <v-btn @click="openGallery" color="primary">
                Open Gallery
            </v-btn>
        </application>
    `
});