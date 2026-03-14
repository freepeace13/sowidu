import ContactCard from '../components/cards/ContactCard';
import ItemCard from '../components/cards/ItemCard';
import Item from '../services/models/item';
import Task from '../services/models/task';
import Contact from '../services/models/contact';
import Photo from './resources/tiger.jpg';
import ImageCard from '~/components/UI/Cards/ImageCard';
import TaskCard from '~/components/cards/TaskCard';
import faker from 'faker';

const _fake = {
    contacts: require('./data/addressbook.json'),
    videos: require('./data/videos.json')
};

const __tasks = [
    new Task({
        id: faker.random.number(),
        title: faker.lorem.sentence(),
        description: faker.lorem.paragraph(),
        members: [],
        attachments: [],
        slug: faker.lorem.slug(),
        docType: '.task'
    }),
    new Task({
        id: faker.random.number(),
        title: faker.lorem.sentence(),
        description: faker.lorem.paragraph(),
        members: [],
        attachments: [],
        slug: faker.lorem.slug(),
        docType: '.task'
    })
];

export default {
    title: 'Cards'
};

export const taskCard = () => ({
    components: {
        TaskCard
    },

    data: () => ({
        tasks: __tasks
    }),

    template: `
        <application>
            <v-layout row wrap>
                <v-flex xs3 v-for="task in tasks" :key="task.id">
                    <TaskCard :task="task"/>
                </v-flex>
            </v-layout>
        </application>
    `
})

export const imageCard = () => ({
    components: {
        ImageCard
    },

    data: () => ({
        progress: 0,
        image: Photo,
        selected: false
    }),

    methods: {
        stop() {
            clearInterval(this.timer);
        },

        reset() {
            this.stop();
            this.progress = 0;
        },

        start() {
            this.stop();

            this.timer = setInterval(() => {
                this.progress = Math.min(this.progress + 1, 100);

                if (this.progress === 100) {
                    this.stop();
                }
            }, 100);
        }
    },

    template: `
        <application>
            <v-btn color="blue" @click="start">Start</v-btn>
            <v-btn color="red" @click="stop">Stop</v-btn>
            <v-btn color="secondary" @click="reset">Reset</v-btn>

            <v-layout row wrap>
                <v-flex xs3>
                    <ImageCard
                        @click.native="selected = !selected"
                        :url="image"
                        :progress="progress"
                        :highlighted="selected"
                    />
                </v-flex>
            </v-layout>
        </application>
    `,
})

export const itemCard = () => ({
    components: {
        ItemCard
    },

    data: () => ({
        item: new Item({
            id: 1,
            name: 'laborum aute consectetur',
            longDescription: 'qui laborum aute consectetur sunt magna excepteur tempor excepteur.',
            offeredPrice: 12,
            fixTradedPrice: 42,
            retailPrice: 50
        })
    }),

    template: `
        <application>
            <v-layout wrap row>
                <v-flex xs12>
                    <ItemCard
                        :item="item"
                        @click:photo="onPhotoClicked"
                        @click:title="onTitleClicked"
                        @menu:edit="onEdit"
                        @menu:delete="onDelete"
                    />
                </v-flex>
            </v-layout>
        </application>
    `,

    methods: {
        onPhotoClicked(item) {
            alert(`Item ID# ${item.id} photo has been clicked`);
        },

        onTitleClicked(item) {
            alert(`Item ID# ${item.id} title has been clicked`);
        },

        onEdit() {
            alert(`Edit item menu has been clicked`);
        },

        onDelete() {
            alert(`Delete item menu has been clicked`);
        }
    }
});

export const contactCard = () => ({
    components: {
        ContactCard
    },

    data: () => ({
        contacts: _fake.contacts.map((i) => new Contact(i))
    }),

    methods: {
        handleEmployment(contact) {
            console.log('employment', contact);
        },

        handleRemove(contact) {
            console.log('remove', contact);
        },

        handleAdd(contact) {
            console.log('add', contact)
        }
    },

    template: `
        <application>
            <v-layout row wrap>
                <v-flex xs1 md2 lg4 v-for="contact in contacts" :key="contact.id">
                    <ContactCard
                        :name="contact.preferredInfo.toString()"
                        :type="contact.contactableType"
                        :employer="contact.originalInfo.employer"
                        :address="contact.preferredInfo.address.toString()"
                        :registered="contact.registered"
                        :connected="contact.addressbooked"
                        :avatar="contact.avatar"
                        :hireable="contact.canInvite()"

                        @menu:employment="handleEmployment(contact)"
                        @menu:add="handleAdd(contact)"
                        @menu:remove="handleRemove(contact)"
                    />
                </v-flex>
            </v-layout>
        </application>
    `,
})