import axios from 'axios';

export default class Customer {
    id;
    name;
    photo;
    clientId;
    clientType;
    organizationName;
    organizationPhoto;
    email;
    client;

    constructor(attributes = {}) {
        this.id = attributes.id;
        this.name = attributes.name;
        this.photo = attributes.photo;
        this.clientId = attributes.client_id;
        this.clientType = attributes.client_type;
        this.email = attributes.email;
        this.organizationName = attributes.organization_name;
        this.organizationPhoto = attributes.organization_photo;
        this.client = attributes.client;
    }

    static async lists() {
        const { data } = await axios.get('/api/client/customers');
        return data.data.map((v) => new Customer(v));
    }
}